<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\posts\Post;
use Livewire\Attributes\Url;
use Jenssegers\Agent\Agent;

new class extends Component
{
    use WithPagination;

    public $postCategory = 'all';

    public $postTypeFilter = 'all';

    #[Url(as: 'search')]
    public $query_search = ''; // * Search Query (set to empty string by default upon page load)

    #[Url(as: 'min_price')]
    public $min_price = '';

    #[Url(as: 'max_price')]
    public $max_price = '';

    public function filterByCategory($category)
    {
        $this->postCategory = $category;
        $this->resetPage(); // Reset pagination to the first page when filtering
    }

    public function updatedPostTypeFilter($type)
    {
        $this->postTypeFilter = $type;
        $this->resetPage(); // Reset pagination to the first page when filtering
    }

    public $isMobile;

    private function getIsMobileProperty()
    {
        return (new Agent())->isMobile();
    }

    // Location Filter:

    public $regions;
    public $provinces;
    public $cities;

    public $region;
    public $province;
    public $city;

    public $filteredProvinces = [];
    public $filteredCities = [];

    public function updatedRegion($value)
    {

        $this->filteredProvinces = collect($this->provinces)->where('region', $value)->values()->all();
        $this->province = ''; // Reset selected province
        $this->filteredCities = []; // Reset cities when region changes
    }

    public function updatedProvince($value)
    {
        $this->filteredCities = collect($this->cities)->where('province', $value)->values()->all();
        $this->city = ''; // Reset selected city
    }


    // Posts Filter


    public function mount(){

        $this->regions = json_decode(file_get_contents(base_path('data/regions.json')), true);
        $this->provinces = json_decode(file_get_contents(base_path('data/provinces.json')), true);
        $this->cities = json_decode(file_get_contents(base_path('data/cities.json')), true);
        $this->isMobile = $this->getIsMobileProperty();
    }





    #[Computed]
    public function posts()
    {
        return Post::query()
            ->where('status','approved')
            ->when(
                $this->postCategory !== 'all',
                fn ($q) => $q->where('category', $this->postCategory)
            )
            ->when(
                $this->postTypeFilter !== 'all',
                fn ($q) => $q->where('listing_type', $this->postTypeFilter)
            )
            ->when($this->query_search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->query_search . '%')
                    ->orWhere('description', 'like', '%' . $this->query_search . '%');
                });
            })
            ->when($this->min_price, fn ($query) => $query->where('price', '>=', $this->min_price))
            ->when($this->max_price, fn ($query) => $query->where('price', '<=', $this->max_price))
            ->when($this->region, function ($query) {
                $query->whereHas('user.personalProfile', function ($q) {
                    $q->when($this->region, fn ($qq) => $qq->where('region', $this->region))
                    ->when($this->province, fn ($qq) => $qq->where('province', $this->province))
                    ->when($this->city, fn ($qq) => $qq->where('city', $this->city));
                });
            })
            ->latest()
            ->paginate(12);
    }


};
