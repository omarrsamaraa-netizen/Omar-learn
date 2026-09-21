<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(): View
    {

        $Items = Item::with('category')->orderBy('created_at', 'desc')->paginate(10);

        return view('main.index', ['category' => $Items]);

    }

    public function trash(): View
    {
        $Items = Item::onlyTrashed()->orderBy('created_at', 'desc')->paginate(10);

        return view('main.trash', ['category' => $Items]);

    }

    public function create(): View
    {
        $categories = Category::all();

        return view('main.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $item = Item::create($this->validatedItem($request));
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'caption' => 'required|string|max:1000',

        ]);

        return redirect()
            ->route('main.index', $item)
            ->with('success', 'Piece added to the collection.');
    }

    public function show(Item $item): View
    {
        $item->load('category');

        return view('main.show', [
            'id' => $item->id,
            'item' => $item,
        ]);
    }

    public function edit(Item $item): View
    {
        return view('main.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $item->update($this->validatedItem($request));

        return redirect()
            ->route('main.show', $item)
            ->with('status', 'Piece updated.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()
            ->route('main.index')
            ->with('status', 'Piece removed from the collection.');
    }

    public function permenentDelete(Item $item): RedirectResponse
    {
        $item->forceDelete();

        return redirect()
            ->route('main.index')
            ->with('status', 'Piece removed completly from the collection.');
    }

    public function emptyTrash(): RedirectResponse
    {
        Item::onlyTrashed()->forceDelete();

        return redirect()
            ->route('main.trash')
            ->with('status', 'The trash is now empty.');
    }

    public function restore(Item $item): RedirectResponse
    {
        if ($item->trashed()) {
            $item->restore();
        }

        return redirect()
            ->route('main.index')
            ->with('status', 'Piece restored successfully to the collection.');
    }

    private function validatedItem(Request $request): array
    {
        return $request->validate([
             'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'integer', 'between:0,100'],
            'caption' => ['required', 'string', 'max:5000'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);
    }
}

// suck my dick
