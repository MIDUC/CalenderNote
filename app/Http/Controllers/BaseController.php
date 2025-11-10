<?php

namespace App\Http\Controllers;

use App\Http\Requests\Base\StoreRequest;
use App\Http\Requests\Base\UpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Repositories\BaseRepository;

abstract class BaseController extends Controller
{

    /**
     * Trả về response thành công
     */
    protected function sendResponse($data, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Trả về response lỗi
     */
    protected function sendError(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Chuẩn hóa tham số filter/sort/pagination từ request
     */
    protected function filterParams(Request $request): array
    {
        $filters = $request->input('filters', []);
        $sortBy = (array) $request->input('sort_by', []);
        $sortDirection = (array) $request->input('sort_direction', []);
        $page = (int) $request->input('page', 1);
        $itemPerPage = (int) $request->input('item_per_page', 10);

        // Bổ sung hướng sort mặc định nếu thiếu
        if (count($sortDirection) < count($sortBy)) {
            $missing = array_fill(0, count($sortBy) - count($sortDirection), 'asc');
            $sortDirection = array_merge($sortDirection, $missing);
        }

        return [
            'filters' => $filters,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'page' => $page,
            'item_per_page' => $itemPerPage,
        ];
    }
}