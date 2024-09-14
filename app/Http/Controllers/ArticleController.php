<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{
    public function index()
    {
        //Mostrar los artículos en el admin
        $user = Auth::user();
        $articles = Article::where('user_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->simplePaginate(10);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        //Obtener categorías públicas
        $categories = Category::select(['id', 'name'])
                                ->where('status', '1')
                                ->get();

        return view('admin.articles.create', compact('categories'));
    }

    public function store(ArticleRequest $request)
    {   

        $request->merge([
            'user_id' => Auth::user()->id,
        ]);

        //Guardo la solicitud en una variable
        $article = $request->all();

        //Validar si hay un archivo en el request
        if($request->hasFile('image')){
            $article['image'] = $request->file('image')->store('articles');
        }

        Article::create($article);

        return redirect()->action([ArticleController::class, 'index'])
                            ->with('success-create', 'Artículo creado con éxito');

    }

    public function show(Article $article)
    {
        $this->authorize('published', $article);

        $comments = $article->comments()->simplePaginate(5);

        return view('subscriber.articles.show', compact('article', 'comments'));
    }

    public function edit(Article $article)
    {
        $this->authorize('view', $article);

        //Obtener categorías públicas
        $categories = Category::select(['id', 'name'])
                                ->where('status', '1')
                                ->get();

        return view('admin.articles.edit', compact('categories', 'article'));
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);

        //Si el usuario sube una nueva imagen
        if($request->hasFile('image')){
            //Eliminar la imagen anterior
            File::delete(public_path('storage/' . $article->image));
            //Asigna la nueva imagen
            $article['image'] = $request->file('image')->store('articles');
        }

        //Actualizar datos
        $article->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'introduction' => $request->introduction,
            'body' => $request->body,
            'user_id' => Auth::user()->id,
            'category_id' => $request->category_id,
            'status' => $request->status,
        ]);

        return redirect()->action([ArticleController::class, 'index'])
                            ->with('success-update', 'Artículo modificado con éxito');
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);

        //Eliminar imagen del artículo
        if($article->image){
            File::delete(public_path('storage/' . $article->image));
        }

        //Eliminar artículo
        $article->delete();

        return redirect()->action([ArticleController::class, 'index'], compact('article'))
            ->with('success-delete', 'Artículo eliminado con éxito');
    }
}
