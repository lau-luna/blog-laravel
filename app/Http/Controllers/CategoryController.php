<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{

    public function index()
    {
        //Mostrar categorías en el admin
        $categories = Category::orderBy('id', 'desc')
                            ->simplePaginate(8);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $category = $request->all();

        //Validar si hay un archivo
        if($request->hasFile('image')){
            $category['image'] = $request->file('image')->store('categories');
        }

        //Guardar información
        Category::create($category);

        return redirect()->action([CategoryController::class, 'index'])
            ->with('success-create', 'Categoría creada con éxito');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        //Si el usuario sube una imagen
        if($request->hasFile('image')){
            //Eliminar imagen anterior
            File::delete(public_path('storage/' . $category->image));
            //Asignar nueva imagen
            $category['image'] = $request->file('image')->store('categories');
        }

        //Actualizar datos
        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'status' => $request->status,
            'is_featured' => $request->is_featured,
        ]);

        return redirect()->action([CategoryController::class, 'index'], compact('category'))
            ->with('success-update', 'Categoría modificada con éxito');
    }

    public function destroy(Category $category)
    {
        //Eliminar imagen de la categoría
        if($category->image){
            File::delete(public_path('storage/' . $category->image));
        }

        $category->delete();

        return redirect()->action([CategoryController::class, 'index'], compact('category'))
            ->with('success-delete', 'Categoría eliminada con éxito');
    }

    //Filtrar artículos por categorías
    public function detail(Category $category){

        $this->authorize('published', $category);

        $articles = Article::where([
            ['category_id', $category->id],
            ['status', '1']
        ])
            ->orderBy('id', 'desc')
            ->simplePaginate(5);
        
        $navbar = Category::where([
            ['status', '1'],
            ['is_featured', '1'],
        ])->paginate(3);     

        return view('subscriber.categories.detail', compact('articles', 'category', 'navbar'));
    }
}
