<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\posts\Post;

new class extends Component
{
    use WithPagination;

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

    public $postTypeFilter = "sell";

    public function mount(){

        $this->regions = json_decode(file_get_contents(base_path('data/regions.json')), true);
        $this->provinces = json_decode(file_get_contents(base_path('data/provinces.json')), true);
        $this->cities = json_decode(file_get_contents(base_path('data/cities.json')), true);

    }





    #[Computed]
    public function posts(){
        return Post::orderBy('created_at', 'desc')->paginate(12);
    }


};
