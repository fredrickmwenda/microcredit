<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Modules\Core\Entities\Menu;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:core.menu.index'])->only(['index', 'show', 'update']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data = Menu::with('children')->where('is_parent', 1)->orderBy('menu_order', 'asc')->get();
        return theme_view('core::menu.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('core::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('core::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('core::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Action not allowed in demo.');
        }

        $data = $request->input('data');

        // Convert "item[]=1&item[]=2..." into array
        parse_str($data, $output);

        if (!isset($output['item']) || !is_array($output['item'])) {
            return response()->json([
                'success' => 0,
                'msg' => 'Menu data is not set correctly.'
            ], 422);
        }

        $ids = $output['item']; // array of IDs
        $p = 0;

        foreach ($ids as $id) {
            $menu = Menu::find($id);
            if ($menu) {
                $menu->menu_order = $p;
                $menu->save();
                $p++;
            }
        }

        activity()->log('Update Menu');

        return response()->json([
            'success' => 1,
            'msg' => trans_choice("core::general.successfully_saved", 1)
        ]);
    }
}
