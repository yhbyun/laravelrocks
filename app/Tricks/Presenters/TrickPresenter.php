<?php

namespace Tricks\Presenters;

use App;
use Carbon\Carbon;
use Misd\Linkify\Linkify;
use MyString;
use Tricks\User;
use Tricks\Trick;
use Tricks\Category;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\HTML;
use McCool\LaravelAutoPresenter\BasePresenter;

class TrickPresenter extends BasePresenter
{
    /**
     * Cache for whether the user has liked this trick.
     *
     * @var bool
     */
    protected $likedByUser = null;

    /**
     * Create a new TrickPresenter instance.
     *
     * @param  \Tricks\Trick  $trick
     * @return void
     */
    public function __construct(Trick $trick)
    {
        $this->resource = $trick;
    }

    /**
     * Get the edit link for this trick.
     *
     * @return string
     */
    public function editLink()
    {
        return URL::route('tricks.edit', [ $this->resource->slug ]);
    }

    /**
     * Get the delete link for this trick.
     *
     * @return string
     */
    public function deleteLink()
    {
        return URL::route('tricks.delete', [ $this->resource->slug ]);
    }

    /**
     * Get a readable created timestamp.
     *
     * @return string
     */
    public function timeago()
    {
        return $this->resource->created_at->diffForHumans();
    }

    /**
     * Get a readable lat updated timestamp.
     *
     * @return string
     */
    public function timeagoLastUpdated()
    {
        // 0000-00-00 00:00:00 값 비교
        if ($this->resource->last_updated_at == Carbon::create(0, 0, 0, 0, 0, 0)->toDateTimeString()) {
            return '';
        }
        return $this->resource->last_updated_at->diffForHumans();
    }


    /**
     * Returns whether the given user has liked this trick.
     *
     * @param  \Tricks\User $user
     * @return bool
     */
    public function likedByUser($user)
    {
        if (is_null($user)) {
            return false;
        }

        if (is_null($this->likedByUser)) {
            $this->likedByUser = $this->resource
                                      ->votes()
                                      ->where('users.id', $user->id)
                                      ->exists();
        }

        return $this->likedByUser;
    }

    /**
     * Get all the categories for this trick.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Tricks\Category[]
     */
    public function allCategories()
    {
        return $this->resource->categories;
    }

    /**
     * List the categories which this trick is in.
     *
     * @return string
     */
    public function categories()
    {
        $result = '';

        if ($this->hasCategories()) {
            $categories = [];

            foreach ($this->resource->categories as $category) {
                $categories[] = $this->getCategoryLink($category);
            }

            $result = 'in ' . implode(', ', $categories);
        }

        return $result;
    }

    /**
     * Determine whether the trick has any categories.
     *
     * @return bool
     */
    protected function hasCategories()
    {
        return isset($this->resource->categories) && count($this->resource->categories) > 0;
    }

    /**
     * Get a HTML link to the given category.
     *
     * @param  \Tricks\Category  $category
     * @return string
     */
    protected function getCategoryLink(Category $category)
    {
        return HTML::linkRoute('tricks.browse.category', $category->name, [ $category->slug ]);
    }

    /**
     * Get the meta description for this trick.
     *
     * @return string
     */
    public function pageDescription()
    {
        $html = $this->convertMarkdown($this->resource->description);
        $description = strip_tags($html);
        $description = str_replace('"', '', $description);

        $maxLength   = 160;

        if (strlen($description) > $maxLength) {
            while (strlen($description) + 3 > $maxLength) {
                $description = $this->removeLastWord($description);
            }

            $description .= '...';
        }

        return e(trim($description));
    }

    // laravel.io의 excerpt 메소드 참고
    public function abstraction()
    {
        $description = $this->resource->description;
        $html = $this->convertMarkdown($description);
        $text = strip_tags($html);

        $maxLength = 255;

        return MyString::cutString($text, $maxLength);
    }

    public function prettyDescription() {
        $description = $this->resource->description;

        $description = $this->convertMarkdown($description);
        //$description = $this->convertNewlines($description);
        $description = $this->formatGists($description);
        $description = $this->linkify($description);

        return $description;
    }

    /**
     * Get the meta title for this trick.
     *
     * @return string
     */
    public function pageTitle()
    {
        $title     = $this->resource->title;
        $baseTitle = ' | Laravel-Tricks.com';
        $maxLength = 70;

        if (strlen($title.$baseTitle) > $maxLength) {
            while (strlen($title.$baseTitle) > $maxLength) {
                $title = $this->removeLastWord($title);
            }
        }

        return e($title);
    }

    /**
     * Remove the last word from a given string.
     *
     * @param  string  $string
     * @return string
     */
    protected function removeLastWord($string)
    {
        $split = explode(' ', $string);

        array_pop($split);

        return implode(' ', $split);
    }

    /**
     * Get the tag URI for this trick.
     *
     * @return string
     */
    public function tagUri()
    {
        $url = parse_url(route('tricks.show', $this->resource->slug));

        $output  = 'tag:';
        $output .= $url['host'] . ',';
        $output .= $this->resource->created_at->format('Y-m-d') . ':';
        $output .= $url['path'];

        return $output;
    }

    private function convertMarkdown($content)
    {
        return App::make('markdown')->convertMarkdownToHtml($content);
    }

    private function convertNewlines($content)
    {
        return str_replace("\n\n", '<br/>', $content);
    }

    private function formatGists($content)
    {
        return App::make('gist')->format($content);
    }

    private function linkify($content)
    {
        $linkify = new Linkify();
        return $linkify->process($content);
    }
}
