<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(): View
    {

        $Items = Item::orderBy('created_at', 'desc')->paginate(10);

        return view('main.index', ['category' => $Items]);
    }

    public function create(): View
    {
        return view('main.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $item = Item::create($this->validatedItem($request));

        return redirect()
            ->route('main.show', $item)
            ->with('status', 'Piece added to the collection.');
    }

    public function show(Item $item): View
    {
        return view('main.show', [
            'id' => $item->id,
            'item' => $item,
        ]);
    }

    public function edit(Item $item): View
    {
        return view('main.edit', ['item' => $item]);
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

    private function validatedItem(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'integer', 'between:0,100'],
            'caption' => ['required', 'string', 'max:5000'],
        ]);
    }
}
