<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'show_locations' => 'in:true,false',
            ]);

            $entity = User::orderBy('updated_at', 'desc');

            if ($request->get('show_locations', 'false') === 'true') {
                $entity->with('locations');
            }

            return response()->json([
                'data' => $entity->paginate(10),
                'message' => '',
                'error' => false,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }

    public function getOne(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'show_locations' => 'in:true,false',
            ]);

            $entity = User::where('id', $id);

            if ($request->get('show_locations', 'false') === 'true') {
                $entity->with('locations');
            }

            if (!$entity->exists()) {
                return response()->json([
                    'message' => 'Пользователь не найдена.',
                    'error' => true,
                ], 404);
            }

            return response()->json([
                'data' => $entity->first(),
                'message' => '',
                'error' => false,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|min:3|max:180',
                'login' => 'required|min:4|max:180|unique:users,login',
                'password' => 'required|min:6|max:180',
            ]);

            $entity = new User();

            $entity->name = $request->get('name');
            $entity->login = $request->get('login');
            $entity->password = bcrypt($request->get('password'));

            if ($entity->save()) {
                return response()->json([
                    'message' => 'Пользователь успешно сохранена.',
                    'error' => false,
                ], 201);
            }

            return response()->json([
                'message' => 'Сбой сохранения.',
                'error' => true,
            ], 400);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'min:3|max:180',
                'login' => "min:4|max:180|unique:users,login,$id",
                'password' => 'min:6|max:180',
            ]);

            $entity = User::where('id', $id)->first();
            if (!$entity) {
                return response()->json([
                    'message' => 'Пользователь не найдена.',
                    'error' => true,
                ], 404);
            }

            if ($request->has('name')) {
                $entity->name = $request->get('name');
            }

            if ($request->has('login')) {
                $entity->login = $request->get('login');
            }

            if ($request->has('password')) {
                $entity->password = bcrypt($request->get('password'));
            }

            if ($entity->save()) {
                return response()->json([
                    'message' => 'Пользователь успешно обновлена.',
                    'error' => false,
                ], 200);
            }

            return response()->json([
                'message' => 'Сбой сохранения.',
                'error' => true,
            ], 400);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }

    public function delete(Request $request, string $id): JsonResponse
    {
        try {
            $entity = User::where('id', $id);

            if (!$entity->exists()) {
                return response()->json([
                    'message' => 'Пользователь не найдена.',
                    'error' => true,
                ], 404);
            }

            $entity = $entity->first();

            if ($entity->id == auth()->id()) {
                return response()->json([
                    'message' => 'Себя нельзя удалять.',
                    'error' => true,
                ], 400);
            }

            if ($entity->is_admin == 1) {
                return response()->json([
                    'message' => 'Администратора нельзя удалять.',
                    'error' => true,
                ], 400);
            }

            if ($entity->delete()) {
                Location::where('user_id', $id)->delete();

                return response()->json([
                    'message' => 'Пользователь успешно удалена.',
                    'error' => false,
                ], 200);
            }

            return response()->json([
                'message' => 'Сбой удаления.',
                'error' => true,
            ], 400);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }
}
