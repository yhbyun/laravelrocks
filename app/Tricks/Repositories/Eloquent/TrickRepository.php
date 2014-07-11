<?php

namespace Tricks\Repositories\Eloquent;

use Disqus;
use MyString;
use Tricks\Tag;
use Tricks\User;
use Tricks\Trick;
use Tricks\Category;
use Illuminate\Support\Str;
use Tricks\Services\Forms\TrickForm;
use Tricks\Services\Forms\TrickEditForm;
use Tricks\Exceptions\TagNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Tricks\Exceptions\CategoryNotFoundException;
use Tricks\Repositories\TrickRepositoryInterface;

class TrickRepository extends AbstractRepository implements TrickRepositoryInterface
{
    /**
     * Category model.
     *
     * @var \Tricks\Category
     */
    protected $category;

    /**
     * Tag model.
     *
     * @var \Tricks\Tag
     */
    protected $tag;

    /**
     * Create a new DbTrickRepository instance.
     *
     * @param  \Tricks\Trick  $trick
     * @param  \Tricks\Category  $category
     * @param  \Tricks\Tag  $tag
     * @return void
     */
    public function __construct(Trick $trick, Category $category, Tag $tag)
    {
        $this->model    = $trick;
        $this->category = $category;
        $this->tag      = $tag;
    }

    /**
     * Find all the tricks for the given user paginated.
     *
     * @param  \Tricks\User $user
     * @param  integer $perPage
     * @return \Illuminate\Pagination\Paginator|\Tricks\Trick[]
     */
    public function findAllForUser(User $user, $perPage = 15)
    {
        $tricks = $user->tricks()->orderBy('created_at', 'DESC')->paginate($perPage);

        return $tricks;
    }

    /**
     * Find all tricks that are favorited by the given user paginated.
     *
     * @param  \Tricks\User $user
     * @param  integer $perPage
     * @return \Illuminate\Pagination\Paginator|\Tricks\Trick[]
     */
    public function findAllFavorites(User $user, $perPage = 15)
    {
        $tricks = $user->votes()->orderBy('created_at', 'DESC')->paginate($perPage);

        return $tricks;
    }

    /**
     * Find a trick by the given id.
     *
     * @param  integer $id
     * @return \Tricks\Trick
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find a trick by the given slug.
     *
     * @param  string $slug
     * @return \Tricks\Trick
     */
    public function findBySlug($slug)
    {
        return $this->model->whereSlug($slug)->first();
    }

    /**
     * Find all the tricks paginated.
     *
     * @param  integer $perPage
     * @return \Illuminate\Pagination\Paginator|\Tricks\Trick[]
     */
    public function findAllPaginated($perPage = 15)
    {
        $tricks = $this->model->whereDraft(0)->orderBy('created_at', 'DESC')->paginate($perPage);

        return $tricks;
    }

    /**
     * Find all tricks order by the creation date paginated.
     *
     * @param  integer $perPage
     * @return \Illuminate\Pagination\Paginator|\Tricks\Trick[]
     */
    public function findMostRecent($perPage = 15)
    {
        return $this->findAllPaginated($perPage);
    }

    /**
     * Find the tricks ordered by the number of comments paginated.
     *
     * @param  integer $perPage
     * @return \Illuminate\Pagination\Paginator|\Tricks\Trick[]
     */
    public function findMostCommented($perPage = 15)
    {
        $tricks = $this->model->whereDraft(0)->orderBy('created_at', 'desc')->get();

        $tricks = Disqus::appendCommentCounts($tricks);

        $tricks = $tricks->sortBy(function ($trick) {
            return $trick->comment_count;
        })->reverse();

        $page = \Input::get('page', 1);
        $skip = ($page - 1) * $perPage;
        $items = $tricks->all();
        array_splice($items, 0, $skip);

        return \Paginator::make($items, count($tricks), $perPage);
    }

    /**
     * Find the tricks ordered by popularity (most liked / viewed) paginated.
     *
     * @param  integer  $perPage
     * @return \Illuminate\Pagination\Paginator|\Tricks\Trick[]
     */
    public function findMostPopular($perPage = 15)
    {
        return $this->model->whereDraft(0)
                    ->orderByRaw('(tricks.vote_cache * 5 + tricks.view_cache) DESC')
                    ->paginate($perPage);
    }

    /**
     * Find the last 15 tricks that were added.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Tricks\Trick[]
     */
    public function findForFeed()
    {
        return $this->model->whereDraft(0)->orderBy('created_at', 'desc')->limit(15)->get();
    }

    /**
     * Find all tricks.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Tricks\Trick[]
     */
    public function findAllForSitemap()
    {
        return $this->model->whereDraft(0)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find all tricks for the category that matches the given slug.
     *
     * @param string $slug
     * @param integer $perPage
     * @return array
     * @throws \Tricks\Exceptions\CategoryNotFoundException
     */
    public function findByCategory($slug, $perPage = 15)
    {
        $category = $this->category->whereSlug($slug)->first();

        if (is_null($category)) {
            throw new CategoryNotFoundException('The category "'.$slug.'" does not exist!');
        }

        $tricks = $category->tricks()->whereDraft(0)->orderBy('created_at', 'DESC')->paginate($perPage);

        return [ $category, $tricks ];
    }

    /**
     * Find all tricks that match the given search term.
     *
     * @param  string $term
     * @param  integer $perPage
     * @return \Illuminate\Pagination\Paginator|\Tricks\Trick[]
     */
    public function searchByTermPaginated($term, $perPage = 15)
    {
        $tricks =  $this->model
                        ->whereDraft(0)
                        ->where(function ($query) use ($term) {
                            $query->Where('title', 'LIKE', '%'.$term.'%')
                                  ->orWhere('description', 'LIKE', '%'.$term.'%')
                                  ->orWhereHas('tags', function ($query) use ($term) {
                                      $query->where('title', 'LIKE', '%' . $term . '%')
                                            ->orWhere('slug', 'LIKE', '%' . $term . '%');
                                  })
                                  ->orWhereHas('categories', function ($query) use ($term) {
                                      $query->where('name', 'LIKE', '%' . $term . '%')
                                      ->orWhere('slug', 'LIKE', '%' . $term . '%');
                                  });
                        })
                        ->orderBy('created_at', 'desc')
                        ->orderBy('title', 'asc')
                        ->paginate($perPage);

        return $tricks;
    }

    /**
     * Get a list of tag ids that are associated with the given trick.
     *
     * @param  \Tricks\Trick $trick
     * @return array
     */
    public function listTagsIdsForTrick(Trick $trick)
    {
        return $trick->tags->lists('id');
    }

    /**
     * Get a list of category ids that are associated with the given trick.
     *
     * @param  \Tricks\Trick $trick
     * @return array
     */
    public function listCategoriesIdsForTrick(Trick $trick)
    {
        return $trick->categories->lists('id');
    }

    /**
     * Create a new trick in the database.
     *
     * @param  array $data
     * @return \Tricks\Trick
     */
    public function create(array $data)
    {
        $trick = $this->getNew();

        $trick->user_id     = $data['user_id'];
        $trick->title       = e($data['title']);
        if ($data['slug']) {
            $trick->slug    = $data['slug'];
        } else {
            $trick->slug    = MyString::slug($data['title'], '-');
        }
        $trick->description = $data['description'];
        $trick->code        = $data['code'];
        $trick->draft       = get_if_set($data['draft'], false);
        $trick->last_updated_at = new \DateTime;

        $trick->save();

        $trick->tags()->sync($data['tags']);
        $trick->categories()->sync($data['categories']);

        return $trick;
    }

    /**
     * Update the trick in the database.
     *
     * @param  \Tricks\Trick $trick
     * @param  array $data
     * @return \Tricks\Trick
     */
    public function edit(Trick $trick, array $data)
    {
        //$trick->user_id = $data['user_id'];
        $trick->title       = e($data['title']);
        if ($data['slug']) {
            $trick->slug    = $data['slug'];
        } else {
            $trick->slug    = MyString::slug($data['title'], '-');
        }
        $trick->description = $data['description'];
        $trick->code        = $data['code'];
        $trick->draft       = get_if_set($data['draft'], false);
        $trick->last_updated_at = new \DateTime;

        $trick->save();

        $trick->tags()->sync($data['tags']);
        $trick->categories()->sync($data['categories']);

        return $trick;
    }

    /**
     * Increment the view count on the given trick.
     *
     * @param  \Tricks\Trick $trick
     * @return \Tricks\Trick
     */
    public function incrementViews(Trick $trick)
    {
        $trick->view_cache = $trick->view_cache + 1;
        $trick->save();

        return $trick;
    }

    /**
     * Find all tricks for the tag that matches the given slug.
     *
     * @param string $slug
     * @param integer $perPage
     * @return array
     * @throws \Tricks\Exceptions\TagNotFoundException
     */
    public function findByTag($slug, $perPage = 15)
    {
        $tag = $this->tag->whereSlug($slug)->first();

        if (is_null($tag)) {
            throw new TagNotFoundException('The tag "' . $slug . '" does not exist!');
        }

        $tricks = $tag->tricks()->whereDraft(0)->orderBy('created_at', 'desc')->paginate($perPage);

        return [ $tag, $tricks ];
    }

    /**
     * Find the next trick that was added after the given trick.
     *
     * @param  \Tricks\Trick  $trick
     * @return \Tricks\Trick|null
     */
    public function findNextTrick(Trick $trick)
    {
        $next = $this->model->where('created_at', '>=', $trick->created_at)
                            ->where('id', '<>', $trick->id)
                            ->whereDraft(0)
                            ->orderBy('created_at', 'asc')
                            ->first([ 'id', 'slug', 'title' ]);

        return $next;
    }

    /**
     * Find the previous trick added before the given trick.
     *
     * @param  \Tricks\Trick  $trick
     * @return \Tricks\Trick|null
     */
    public function findPreviousTrick(Trick $trick)
    {
        $prev = $this->model->where('created_at', '<=', $trick->created_at)
                            ->where('id', '<>', $trick->id)
                            ->whereDraft(0)
                            ->orderBy('created_at', 'desc')
                            ->first([ 'id', 'slug', 'title' ]);

        return $prev;
    }

    /**
     * Check if the user owns the trick corresponding to the given slug.
     *
     * @param  string  $slug
     * @param  mixed   $userId
     * @return bool
     */
    public function isTrickOwnedByUser($slug, $userId)
    {
        return $this->model->whereSlug($slug)->whereUserId($userId)->exists();
    }

    /**
     * Get the trick creation form service.
     *
     * @return \Tricks\Services\Forms\TrickForm
     */
    public function getCreationForm()
    {
        return new TrickForm;
    }

    /**
     * Get the trick edit form service.
     *
     * @param $id
     * @return TrickEditForm
     */
    public function getEditForm($id)
    {
        return new TrickEditForm($id);
    }
}
