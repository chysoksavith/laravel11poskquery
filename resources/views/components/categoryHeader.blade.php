<div class="card mb-4">
    <div class="card-header">
        <h4>Categories</h4>
    </div>
    <div class="card-body">
        @if (isset($categories))
            <!-- Only display the categories if $categories is set -->
            <ul>
                @foreach ($categories as $sideCategory)
                    <li>
                        <a href="{{ route('pos.categories.show', $sideCategory->slug) }}"
                            class="{{ isset($category) && $category->id == $sideCategory->id ? 'active' : '' }}">
                            {{ $sideCategory->category_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No categories to display</p>
        @endif
    </div>
</div>
