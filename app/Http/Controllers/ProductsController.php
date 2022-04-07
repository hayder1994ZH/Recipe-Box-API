<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Helpers\Utilities;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Repository\ProductsRepository;

class ProductsController extends Controller
{
    private $ProductsRepository;
    public function __construct()
    {
        $this->ProductsRepository = new ProductsRepository(new Products());
    }
    public function index(Request $request)
    {
       $request->validate([
           'skip' => 'Integer',
           'take' => 'required|Integer'
       ]);
        $relations = [];
        $filter = ['recipe', 'ingredients', 'directions'];
        $take = $request->take;
        $skip = $request->skip;       
        return $this->ProductsRepository->getList($skip, $take, $relations, $filter);
    }
     
    public function show($id)
    {
        return $this->ProductsRepository->getProductById($id);
    }

    public function store(Request $request)
    {
        $product = $request->validate([
            'recipe' => 'nullable|string|unique:products,recipe',
            'ingredients' => 'nullable|string',
            'directions' => 'nullable|string',
            'productId' => 'required|string'
        ]);
        return $this->ProductsRepository->create($product);
    }

    public function update(Request $request, $id)
    {
        $product = $request->validate([
            'recipe' => 'string',
            'ingredients' => 'string',
            'directions' => 'string'
        ]);
        $productsModel = Products::where('productId', '!=', $id)->where('recipe', $product['recipe'])->first();
        return ($productsModel)? Utilities::wrap(['error' => 'recipe is already been taken'], 400) : $this->ProductsRepository->updateProduct($id, $product);
    }

    public function destroy($id)
    {
        $productsModel = Products::where('productId', $id)->firstOrFail();
        return $this->ProductsRepository->delete($productsModel);
         
    }
}
