@section('title', trans('tricks.trick'))

@section('styles')
	<link rel="stylesheet" href="{{ URL::asset('css/highlight/laratricks.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('js/selectize/css/selectize.bootstrap3.css') }}">
    {{{ stylesheet_link_tag('ghost/ghost') }}}
	<style type="text/css">
	#editor-content {
		position: relative;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		height: 100px;
		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
		border-radius: 4px;
		border: 1px solid #cccccc;
	}
	</style>
@stop

@section('scripts')
	<script type="text/javascript" src="{{url('js/selectize/js/standalone/selectize.min.1.js')}}"></script>
	<script src="//d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js"></script>
	<script type="text/javascript" src="{{ asset('js/trick-new-edit.js') }}"></script>
    {{{ javascript_include_tag('ghost/ghost') }}}
<script>
    jQuery(function($) {
        $('#save-trick-form').submit(function (e) {
            // submit시 textarea에 자동 설정되는 값에는 img 마크다운에 대한 마킹이 추가됨
            // ![<1>](...)
            // 이를 피하기 위해서 폼을 두개로 나누고 설명에 대한 값은 여기서 설정함
            $('#trick-desc').val(window.Ghost.editor.value());
            $('#trick-title').val($('#trick-fake-title').val());
            $('#trick-slug').val($('#trick-slug-title').val());
        });
    });
</script>
@stop

@section('content')
	<div class="container">
		<div class="row">
            <div class="col-xs-12">
				<div class="content-box">
					@if(Auth::check() && (Auth::user()->id == $trick->user_id))
						<div class="pull-right">
							<a data-toggle="modal" href="#deleteModal">{{ trans('tricks.delete') }}</a> |
							<a href="{{ route('tricks.show', [$trick->id, $trick->slug]) }}">View</a>
							@include('tricks.delete',['link'=>$trick->deleteLink])
						</div>
					@endif
					<h1 class="page-title">
						{{ trans('tricks.editing_trick') }}
					</h1>
					@if(Session::get('errors'))
					    <div class="alert alert-danger alert-dismissable">
					        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					         <h5>{{ trans('tricks.errors_while_editing') }}</h5>
					         @foreach($errors->all('<li>:message</li>') as $message)
					            {{$message}}
					         @endforeach
					    </div>
					@endif
					@if(Session::has('success'))
					    <div class="alert alert-success alert-dismissable">
					        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					         <h5>{{ Session::get('success') }}</h5>
					    </div>
					@endif
					{{ Form::open(array('class'=>'form-vertical','id'=>'save-fake-form','role'=>'form'))}}
					    <div class="form-group">
					    	<label for="title">{{ trans('tricks.title') }}</label>
					    	{{Form::text('title', $trick->title, array('class'=>'form-control','placeholder'=>trans('tricks.title_placeholder'),'id'=>'trick-fake-title'))}}
					    </div>
                        <div class="form-group">
                            <label for="slug">{{ trans('tricks.slug') }}</label>
                            {{Form::text('slug', $trick->slug, array('class'=>'form-control','placeholder'=>trans('tricks.slug_placeholder'),'id'=>'trick-slug-title'))}}
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <div class="editor">
                                <div class="outer">
                                    <div class="editorwrap">
                                        <section class="entry-markdown active">
                                            <header class="floatingheader">
                                                <small>Markdown</small>
                                                <a class="markdown-help" href="https://help.github.com/articles/github-flavored-markdown""><span class="hidden">What is Markdown?</span></a>
                                            </header>
                                            <section id="entry-markdown-content" class="entry-markdown-content" data-filestorage="true">
                                                {{Form::textarea('description', htmlspecialchars($trick->description), array('id' => 'entry-markdown', 'placeholder'=>trans('tricks.trick_description_placeholder')));}}
                                            </section>
                                        </section>

                                        <section class="entry-preview">
                                            <header class="floatingheader">
                                                <small>Preview <span class="entry-word-count js-entry-word-count">0 words</span></small>
                                            </header>
                                            <section class="entry-preview-content">
                                                <div class="rendered-markdown"></div>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{Form::close()}}

                    {{ Form::open(array('class'=>'form-vertical','id'=>'save-trick-form','role'=>'form'))}}
                    {{ Form::hidden('title', $trick->title, array('id'=>'trick-title')) }}
                    {{ Form::hidden('slug', null, array('id'=>'trick-slug')) }}
                    {{ Form::hidden('description', null, array('id'=>'trick-desc')) }}
					    <div class="form-group">
					      <label>{{ trans('tricks.trick_code') }}</label>
					      <div id="editor-content" class="content-editor"></div>
					      {{Form::textarea('code', $trick->code, ['id'=>'code-editor','style'=>'display:none;']);}}
					    </div>
					    <div class="form-group">
					    	{{ Form::select('tags[]', $tagList, $selectedTags, array('multiple','id'=>'tags','placeholder'=>trans('tricks.tag_trick_placeholder'),'class' => 'form-control')) }}
					    </div>
					    <div class="form-group">
					    	{{ Form::select('categories[]', $categoryList, $selectedCategories, array('multiple','id'=>'categories','placeholder'=>trans('tricks.categorize_trick_placeholder'),'class' => 'form-control')) }}
					    </div>
                        <div class="form-group">
                            <label>{{ trans('tricks.draft') }}</label>
                            {{ Form::checkbox('draft', true, $trick->draft) }}
                        </div>
					    <div class="form-group">
					        <div class="text-right">
					          <button type="submit"  id="save-section" class="btn btn-primary ladda-button" data-style="expand-right">
					            {{ trans('tricks.update_trick') }}
					          </button>
					        </div>
					    </div>
					{{Form::close()}}
				</div>
			</div>
		</div>
	</div>
@stop
