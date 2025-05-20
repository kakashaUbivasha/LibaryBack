<?php

use App\Http\Controllers\AI\NplController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Book\BookController;
use App\Http\Controllers\Book\FavoriteController;
use App\Http\Controllers\Book\GenreController;
use App\Http\Controllers\Book\ReservationController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/genres/import', [GenreController::class, 'import']);
Route::get('/books/random', [BookController::class, 'random']);
Route::get('/books/{book}', [BookController::class, 'show']);
Route::group(['middleware'=>['auth:sanctum','token.expiration']], function(){
    Route::get('/user', [UserController::class, 'index']);
});
//Route::middleware(['auth:sanctum','token.expiration'])->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::post('/logout', \App\Http\Controllers\Auth\LogoutController::class);
//    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorite', [FavoriteController::class, 'store']);
    Route::delete('/favorite', [FavoriteController::class, 'destroy']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservation', [ReservationController::class, 'store']);
    Route::put('/reservation/canceled', [ReservationController::class, 'canceledReserv']);
    Route::get('/reservation/history', [ReservationController::class, 'history']);
    Route::post('/book/comment', [CommentController::class, 'store']);
    Route::put('/book/comment/{comment}', [CommentController::class, 'update']);
    Route::delete('/book/comment/{comment}', [CommentController::class, 'destroy']);
    Route::get('/top-users', [UserController::class, 'topUsers']);
    Route::post('/ai/recommendations', [\App\Http\Controllers\AI\RecommendationController::class, 'index']);
});
Route::post('/npl/suggest-tags', [NplController::class, 'index']);
Route::post('/register', \App\Http\Controllers\Auth\RegisterController::class);
Route::post('/login', LoginController::class);
Route::get('/books', [BookController::class, 'index']);
Route::get('/book/search', [BookController::class, 'search']);
Route::get('/books/top', [BookController::class, 'top']);
Route::get('/books/{book}/comments', [CommentController::class, 'index']);
Route::get('/guest/{id}', [UserController::class, 'guest']);
Route::get('/genres', [GenreController::class, 'index']);
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
    Route::get('/admin/reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'index']);
    Route::patch('admin/reservation/issuance', [ReservationController::class, 'issuance']);
    Route::patch('admin/reservation/returned', [ReservationController::class, 'returnedBook']);
});

