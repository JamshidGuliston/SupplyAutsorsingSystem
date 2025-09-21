<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\order_product;
use App\Models\order_product_structure;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{

    public function getActiveOrderProductsWithStructures(Request $request)
    {
        try {
            // Pagination parametrlari
            $perPage = $request->get('per_page', 4); // Har sahifada 4 ta order_title
            $page = $request->get('page', 1);
            
            // order_title bo'yicha guruhlash va pagination
            $orderTitles = order_product::where('shop_id', 0)
                            ->select('order_title', \DB::raw('MAX(created_at) as created_at'))
                            ->groupBy('order_title')
                            ->orderBy('created_at', 'desc')
                            ->paginate($perPage, ['*'], 'page', $page);

            $formattedData = [];
            
            foreach ($orderTitles as $orderTitle) {
                // Har bir order_title uchun barcha order_productlarni olish
                $orderProducts = order_product::where('shop_id', 0)
                    ->where('order_title', $orderTitle->order_title)
                    ->with([
                        'orderProductStructures.product',
                        'day',
                        'kinggarden',
                        'shop'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $groupedProducts = $orderProducts->map(function ($orderProduct) {
                    return [
                        'id' => $orderProduct->id,
                        'order_title' => $orderProduct->order_title,
                        'kingar_name_id' => $orderProduct->kingar_name_id,
                        'document_processes_id' => $orderProduct->document_processes_id,
                        'shop_id' => $orderProduct->shop_id,
                        'parent_id' => $orderProduct->parent_id,
                        'created_at' => $orderProduct->created_at,
                        'updated_at' => $orderProduct->updated_at,
                        
                        'kinggarden' => $orderProduct->kinggarden ? [
                            'id' => $orderProduct->kinggarden->id,
                            'name' => $orderProduct->kinggarden->kingar_name ?? null,
                        ] : null,
                        
                        'shop' => $orderProduct->shop ? [
                            'id' => $orderProduct->shop->id,
                            'shop_name' => $orderProduct->shop->shop_name ?? null,
                        ] : null,
                        
                        'products' => $orderProduct->orderProductStructures->map(function ($structure) {
                            return [
                                'id' => $structure->id,
                                'product_name_id' => $structure->product_name_id,
                                'product_weight' => $structure->product_weight,
                                'actual_weight' => $structure->actual_weight,
                                'product' => $structure->product ? [
                                    'id' => $structure->product->id,
                                    'product_name' => $structure->product->product_name,
                                    'category' => $structure->product->category ? [
                                        'id' => $structure->product->category->id,
                                        'category_name' => $structure->product->category->pro_cat_name ?? null,
                                    ] : null,
                                    'size' => $structure->product->size ? [
                                        'id' => $structure->product->size->id,
                                        'size_name' => $structure->product->size->size_name ?? null,
                                    ] : null,
                                ] : null,
                            ];
                        }),
                    ];
                });

                $formattedData[] = [
                    'order_title' => $orderTitle->order_title,
                    'order_count' => $groupedProducts->count(),
                    'orders' => $groupedProducts
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Faol malumotlar muvaffaqiyatli olindi',
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $orderTitles->currentPage(),
                    'last_page' => $orderTitles->lastPage(),
                    'per_page' => $orderTitles->perPage(),
                    'total' => $orderTitles->total(),
                    'from' => $orderTitles->firstItem(),
                    'to' => $orderTitles->lastItem(),
                ],
                'links' => [
                    'first' => $orderTitles->url(1),
                    'last' => $orderTitles->url($orderTitles->lastPage()),
                    'prev' => $orderTitles->previousPageUrl(),
                    'next' => $orderTitles->nextPageUrl(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}