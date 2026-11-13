<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Catagory;
use App\Models\ContactMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    public function index()  
    {
        return view('frontend.index');
    }

    public function contact() 
    {
        return view('contact');
    }

    public function aboutUs()
    {
        return view('frontend.about');
    }

    public function contactStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($data);

        return back()->with('message', 'Thank you for contacting us!');
    }

     public function catagories()
    {
        $catagories=Catagory::where('status','0')->get();
        return view('frontend.collections.catagory.index',compact('catagories'));
    }

    public function products($catagory_slug)  
    {
        $catagory= Catagory::where('slug',$catagory_slug)->first();
        if($catagory){
            
            return view('frontend.collections.products.index',compact('catagory'));
        }else{
            abort(404);
        }
        
    }
    public function productView(string $catagory_slug, string $product_slug)
    {
        $catagory= Catagory::where('slug',$catagory_slug)->first();
        if($catagory){
            $product = $catagory->products()->where('slug',$product_slug)->where('status','0')->first();
            if($product)
            {
                return view('frontend.collections.products.view',compact('product','catagory'));

            }else{
                abort(404);
            }
            
        }else{
            abort(404);
        }
    }
    public function thankyou()
    {
        return view('frontend.thank-you');  
    }

    public function newArrivals()
    {
        // Show recent products as "New Arrivals"
        $newProducts = Product::with(['productImages', 'catagory'])->where('status','0')
                        ->orderBy('created_at','desc')
                        ->paginate(24);

        return view('frontend.new-arrivals', compact('newProducts'));
    }

    public function featuredProducts()
    {
        $featuredProducts = Product::with(['productImages', 'catagory'])->where('is_featured', true)->where('status', '0')->get();
        return view('frontend.collections.featured', compact('featuredProducts'));
    }

    public function trendingProducts()
    {
        $trendingProducts = Product::with(['productImages', 'catagory'])->where('trending', '1')->where('status', '0')->get();
        return view('frontend.collections.trending', compact('trendingProducts'));
    }

    public function search(Request $request)
    {
        $searchTerm = trim((string) $request->query('q', ''));
        if ($searchTerm === '') {
            return redirect('/');
        }

        $words = preg_split('/\s+/', mb_strtolower($searchTerm), -1, PREG_SPLIT_NO_EMPTY);

        $query = Product::with(['catagory', 'productImages'])->where('status', '0');

        // Every word must appear in the product name, brand, search keywords,
        // small description, description, or category name.
        foreach ($words as $word) {
            $like = '%' . addcslashes($word, '\\%_') . '%';
            $query->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(products.name) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(products.brand) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(products.search_keywords) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(products.small_description) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(products.description) LIKE ?', [$like])
                  ->orWhereHas('catagory', function ($c) use ($like) {
                      $c->whereRaw('LOWER(catagories.name) LIKE ?', [$like]);
                  });
            });
        }

        $products = $query->orderBy('created_at', 'desc')
                          ->paginate(12)
                          ->withQueryString();

        return view('frontend.search', compact('products', 'searchTerm'));
    }
}
