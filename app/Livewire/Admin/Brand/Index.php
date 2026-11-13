<?php

namespace App\Livewire\Admin\Brand;

use Str;
use App\Models\Brand;
use App\Models\Catagory;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme='bootstrap';
    // Status is not part of the add/edit forms: it is changed only with toggleStatus().
    public $name, $slug, $brand_id, $catagory_id;
    public function rules()
    {
        return[
            'name'=> 'required|string',
            'slug'=>'required|string',
            'catagory_id'=>'required|integer',
        ];
    }

    public function resetInput()
    {
        $this->name = NULL;
        $this->slug = NULL;
        $this->brand_id = NULL;
        $this->catagory_id = NULL;
    }
    public function storeBrand()
    {
        $validatedData = $this->validate();
        Brand::create([
            'name'=> $this->name,
            'slug'=>Str::slug ($this->slug),
            'status'=> '0', // new brands start Visible
            'catagory_id'=>$this->catagory_id
        ]);
        session()->flash('message','Brand Added Successfully');
        $this->dispatch('closeModal');
        $this->resetInput();

    }
    public function closeModal()
    {
        $this->resetInput();
    }
    public function openModal()
    {
        $this->resetInput();
    }

    public function editBrand(int $brand_id)
    {
        $this->brand_id= $brand_id;
        $brand = Brand::findOrFail($brand_id);
        $this->name = $brand->name;
        $this->slug = $brand->slug;
        $this->catagory_id = $brand->catagory_id;

    }
    public function updateBrand()
    {
        $validatedData = $this->validate();
        // Status is deliberately left out so editing never un-hides a Hidden brand.
        Brand::findOrFail($this->brand_id)->update([
            'name'=> $this->name,
            'slug'=>Str::slug ($this->slug),
            'catagory_id'=>$this->catagory_id
        ]);
        session()->flash('message','Brand Updated Successfully');
        $this->dispatch('closeModal');
        $this->resetInput();

    }

    /** One-click Visible/Hidden switch (status '1' = Hidden, '0' = Visible). */
    public function toggleStatus($brand_id)
    {
        $brand = Brand::findOrFail($brand_id);
        $brand->status = $brand->status == '1' ? 0 : 1;
        $brand->save();

        session()->flash('message', 'Brand is now ' . ($brand->status == 1 ? 'Hidden' : 'Visible'));
    }

    public function deleteBrand($brand_id)
    {
        $this->brand_id = $brand_id; //Storing id

    }

   public function destroyBrand()
   {
        Brand::findOrFail($this->brand_id)->delete();
        session()->flash('message','Brand Deleted Successfully');
        $this->dispatch('closeModal');
        $this->resetInput();
   }

    public function render()
    {
        // Add modal: Visible categories only.
        $catagories = Catagory::where('status','0')->get();

        // Update modal: the same list, plus the edited brand's CURRENT category even when it is
        // Hidden (otherwise the required dropdown comes up empty and the form cannot be saved).
        $updateCatagories = $catagories;
        if ($this->brand_id) {
            $currentCatagoryId = Brand::whereKey($this->brand_id)->value('catagory_id');
            if ($currentCatagoryId && !$catagories->contains('id', $currentCatagoryId)) {
                $current = Catagory::find($currentCatagoryId);
                if ($current) {
                    $updateCatagories = $catagories->concat([$current]);
                }
            }
        }

        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('livewire.admin.brand.index',['brands'=>$brands, 'catagories' =>$catagories, 'updateCatagories' => $updateCatagories])
        ->extends('layouts.admin')
        ->section('content');
    }
}
