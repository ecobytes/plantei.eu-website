@if (!is_null($category->parent))
    @include ('forum::partials.breadcrumb-categories', ['category' => $category->parent])
@endif
<li><a style="color: black !important;" href="{{ Forum::route('category.show', $category) }}">{{ $category->title }}</a></li>
