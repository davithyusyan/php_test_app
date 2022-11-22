<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'show_user' => 'in:true,false',
                'filter_user_ip' => 'array',
                'filter_user_ip.*' => 'ip',
                'filter_user_id' => 'array',
                'filter_user_id.*' => 'uuid',
            ]);


            $entity = Location::orderBy('updated_at', 'desc');

            if ($request->get('show_user', 'false') === 'true') {
                $entity->with('user');
            }

            $userIDs = $request->get('filter_user_id', []);
            if (sizeof($userIDs) != 0) {
                $entity->whereIn('user_id', $userIDs);
            }

            $userIPs = $request->get('filter_user_ip', []);
            if (sizeof($userIPs) != 0) {
                $entity->whereIn('ip', $userIPs);
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
                'show_user' => 'in:true,false',
            ]);

            $entity = Location::where('id', $id);

            if ($request->get('show_user', 'false') === 'true') {
                $entity->with('user');
            }

            if (!$entity->exists()) {
                return response()->json([
                    'message' => 'Локация не найдена.',
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
                'user_id' => 'required|uuid|exists:users,id',
                'ip' => 'required|ip',
                'cord_x' => 'required|numeric|between:-180,180',
                'cord_y' => 'required|numeric|between:-90,90'
            ]);

            $entity = new Location();

            $entity->user_id = $request->get('user_id');
            $entity->ip = $request->get('ip');
            $entity->cord_x = $request->get('cord_x');
            $entity->cord_y = $request->get('cord_y');

            if ($entity->save()) {
                return response()->json([
                    'message' => 'Локация успешно сохранена.',
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
                'user_id' => 'uuid|exists:users,id',
                'ip' => 'ip',
                'cord_x' => 'required|numeric|between:-180,180',
                'cord_y' => 'required|numeric|between:-90,90'
            ]);

            $entity = Location::where('id', $id)->first();
            if (!$entity) {
                return response()->json([
                    'message' => 'Локация не найдена.',
                    'error' => true,
                ], 404);
            }

            $entity->user_id = $request->get('user_id', $entity->user_id);
            $entity->ip = $request->get('ip', $entity->ip);
            $entity->cord_x = $request->get('cord_x', $entity->cord_x);
            $entity->cord_y = $request->get('cord_y', $entity->cord_y);

            if ($entity->save()) {
                return response()->json([
                    'message' => 'Локация успешно обновлена.',
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
            if (!Location::where('id', $id)->exists()) {
                return response()->json([
                    'message' => 'Локация не найдена.',
                    'error' => true,
                ], 404);
            }

            if (Location::destroy($id)) {
                return response()->json([
                    'message' => 'Локация успешно удалена.',
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
