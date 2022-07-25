<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;

class ProductController extends Controller
{
    public $tableName = "product";
    public $visible = ["id", "name", "created_at"];

    public function index(Request $request)
    {
        $length = intval($request->length);
        $length = $length > 0 ? $length : 10;

        $sortBy = $request->sortBy ?? "created_at";
        $sortType = $request->sortType ?? "asc";
        $statusCode = 200;
        $data = null;
        $message = null;
        $success = true;
        try {
            if($sortBy !== "name" && $sortBy !== "created_at") throw new Exception("Invalid sort column!");
            if($sortType !== "asc" && $sortType !== "desc") throw new Exception("Invalid sort type!");

            $query = DB::table($this->tableName);
            $query = $query->orderBy($sortBy, $sortType);
            $query = $query->select($this->visible);
            $data = $query->simplePaginate($length);
        } catch(Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $statusCode = 400;
        }
        $response = [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ];
        return response()->json($response, $statusCode);
    }

    public function indexByCategory(Request $request)
    {
        $length = $request->length ?? 10;
        $statusCode = 200;
        $data = null;
        $message = null;
        $success = true;
        $sortBy = $request->sortBy ?? "created_at";
        $sortType = $request->sortType ?? "asc";
        try {
            if($sortBy !== "name" && $sortBy !== "created_at") throw new Exception("Invalid sort column!");
            if($sortType !== "asc" && $sortType !== "desc") throw new Exception("Invalid sort type!");
            $categoryQuery = DB::table("product_category");
            $query = DB::table($this->tableName);
            $query = $query->select("productCategoryId");
            $query = $query->addSelect([
                DB::raw("CONCAT('[', GROUP_CONCAT(JSON_OBJECT('name', `name`)), ']') AS products")
            ]);
            $query = $query->groupBy("productCategoryId");

            $categoryQuery = $categoryQuery->leftJoinSub($query, "productQuery", function($join) {
                $join->on("product_category.id", "productQuery.productCategoryId");
            });
            $categoryQuery = $categoryQuery->select(
                "product_category.id",
                "product_category.name",
                "product_category.created_at",
                "productQuery.products"
            );
            $categoryQuery = $categoryQuery->orderBy($sortBy, $sortType);
            $data = $categoryQuery->get();
            $data = $data->map(function($value) {
                $value->products = json_decode($value->products);

                return $value;
            });
        } catch(Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $statusCode = 400;
        }
        $response = [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ];
        return response()->json($response, $statusCode);
    }

    public function check($id) {
        $query = DB::table($this->tableName);
        $count = $query->whereId($id)->count("id");
        if($count === 0) throw new Exception("Data not found");

        return;
    }

    public function show($id) {
        $statusCode = 200;
        $data = null;
        $message = null;
        $success = true;
        try {
            $this->check($id);
            $query = DB::table($this->tableName);

            $query = $query->join("product_category", "product_category.id", "product.productCategoryId");

            $query = $query->select(
                "product.*",
                "product_category.name AS productCategoryName"
            );
            $query = $query->where($this->tableName . ".id", $id);

            $detailQuery = DB::table("product_detail");
            $detailQuery = $detailQuery->where("productId", $id);
            $detailData = $detailQuery->get();

            $data = $query->first();
            $data->productDetail = $detailData;
        } catch(Exception $e) {
            $success = false;
            $statusCode = 400;
            $message = $e->getMessage();
        }

        $response = [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ];

        return response()->json($response, $statusCode);

    }
}
