<?php

namespace App\Http\Livewire\Admin\Driver;

use App\Models\Admin\DriverAvailability;
use Livewire\Component;
use Livewire\WithPagination;

class AvailableDriverComponent extends Component
{
    use WithPagination;
    public $search;
    public $sorting = 'desc';
    public $perPage = 10;
    public function render()
    {
        $results = DriverAvailability::searchByDriverName($this->search, $this->sorting, $this->perPage);
        return view('livewire.admin.driver.available-driver-component',['results'=>$results]);
    }
}
