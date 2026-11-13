<?php

namespace App\Livewire\Frontend\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $catagory, $brandInputs=[], $priceInput;
    protected $queryString = [
        'brandInputs'=> ['except' => '', 'as' => 'brand'],
        'priceInput'=> ['except' => '', 'as' => 'price'],
];
    public function mount( $catagory)
    {

        $this->catagory = $catagory;

    }

    // A changed filter can leave the customer on a page that no longer exists.
    public function updatedBrandInputs()
    {
        $this->resetPage();
    }

    public function updatedPriceInput()
    {
        $this->resetPage();
    }

    // Filtering
    public function render()
    {
        $products = Product::with(['productImages', 'catagory'])
        ->where('catagory_id', $this->catagory->id)
        ->when($this->brandInputs,function($q){
            $q->whereIn('brand',$this->brandInputs);
        })
         ->when($this->priceInput,function($q){
            $q->when($this->priceInput=='high-to-low',function($q2){
                $q2->orderBy('selling_price','DESC');
            });
            $q->when($this->priceInput=='low-to-high',function($q2){
                $q2->orderBy('selling_price','ASC');
            });
        })


        ->where('status','0')
        ->paginate(12);
        return view('livewire.frontend.product.index',[
            'products' =>$products,
            'catagory' =>$this->catagory,
        ]);
    }
public function logBrandInputs()
{
    logger($this->brandInputs);
}
public function logpriceInput()
{
    logger($this->priceInput);
}

}
