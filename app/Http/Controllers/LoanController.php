<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;

/**
 * @OA\Info(
 * title="Service Peminjaman API",
 * version="1.0.0",
 * description="Dokumentasi layanan peminjaman buku untuk Tugas 2 IAE"
 * )
 * @OA\Server(
 * url="http://127.0.0.1:8000",
 * description="Local Server"
 * )
 */
class LoanController extends Controller
{
    private function formatResponse($status, $message, $data = null, $code = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($status === 'success') {
            $response['meta'] = [
                'service_name' => 'Peminjaman-Service',
                'api_version' => 'v1'
            ];
        }

        return response()->json($response, $code);
    }

    /**
     * @OA\Get(
     * path="/api/v1/loans",
     * summary="Ambil semua transaksi peminjaman",
     * tags={"Loans"},
     * @OA\Parameter(
     * name="X-IAE-KEY",
     * in="header",
     * required=true,
     * description="API Key Authentication berupa NIM",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Data retrieved successfully"
     * )
     * )
     */
    public function index()
    {
        $loans = Loan::all();
        return $this->formatResponse('success', 'Data retrieved successfully', $loans, 200);
    }

    /**
     * @OA\Get(
     * path="/api/v1/loans/{id}",
     * summary="Ambil detail satu transaksi",
     * tags={"Loans"},
     * @OA\Parameter(
     * name="X-IAE-KEY",
     * in="header",
     * required=true,
     * description="API Key Authentication berupa NIM",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID Transaksi Peminjaman",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Data retrieved successfully"),
     * @OA\Response(response=404, description="Resource not found")
     * )
     */
    public function show($id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return $this->formatResponse('error', 'Resource not found', null, 404);
        }

        return $this->formatResponse('success', 'Data retrieved successfully', $loan, 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/loans",
     * summary="Buat transaksi peminjaman baru",
     * tags={"Loans"},
     * @OA\Parameter(
     * name="X-IAE-KEY",
     * in="header",
     * required=true,
     * description="API Key Authentication berupa NIM",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"member_id","book_id"},
     * @OA\Property(property="member_id", type="integer", example=1),
     * @OA\Property(property="book_id", type="integer", example=5)
     * )
     * ),
     * @OA\Response(response=201, description="Data created successfully")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer',
            'book_id' => 'required|integer',
        ]);

        $loan = Loan::create([
            'member_id' => $request->member_id,
            'book_id' => $request->book_id,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'active'
        ]);

        return $this->formatResponse('success', 'Data created successfully', $loan, 201);
    }

    /**
     * @OA\Post(
     * path="/api/v1/loans/{id}/return",
     * summary="Proses pengembalian buku",
     * tags={"Loans"},
     * @OA\Parameter(
     * name="X-IAE-KEY",
     * in="header",
     * required=true,
     * description="API Key Authentication berupa NIM",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID Transaksi Peminjaman",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Book returned successfully"),
     * @OA\Response(response=404, description="Resource not found")
     * )
     */
    public function returnBook($id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return $this->formatResponse('error', 'Resource not found', null, 404);
        }

        if ($loan->status === 'returned') {
            return $this->formatResponse('error', 'Book is already returned', null, 400);
        }

        $loan->update([
            'return_date' => now()->format('Y-m-d'),
            'status' => 'returned'
        ]);

        return $this->formatResponse('success', 'Book returned successfully', $loan, 200);
    }
}