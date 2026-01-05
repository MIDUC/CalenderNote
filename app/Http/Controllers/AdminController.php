<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LevelName;
use App\Models\LevelXpRequirement;
use App\Models\Item;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\GameRate;
use App\Models\Monster;
use App\Models\NPC;
use App\Models\StoryRule;
use App\Models\Story;
use App\Models\StoryCharacter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends BaseController
{
    /**
     * Get admin dashboard statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_tasks' => Task::count(),
            'total_schedules' => Schedule::count(),
            'total_items' => Item::count(),
            'total_levels' => LevelName::count(),
            'active_users' => User::where('last_login_date', '>=', now()->subDays(7))->count(),
        ];

        return $this->sendResponse($stats, 'Dashboard statistics retrieved successfully.');
    }

    /**
     * Get all users with pagination
     */
    public function users(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->sendResponse($users, 'Users retrieved successfully.');
    }

    /**
     * Get user details
     */
    public function getUser($id)
    {
        $user = User::with(['tasks', 'schedules'])->find($id);

        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'role' => 'sometimes|string|in:admin,user',
            'level' => 'sometimes|integer|min:1',
            'exp' => 'sometimes|integer|min:0',
            'currency' => 'sometimes|integer|min:0',
            'password' => 'sometimes|string|min:6',
            'hp' => 'sometimes|integer|min:0',
            'max_hp' => 'sometimes|integer|min:0',
            'attack' => 'sometimes|integer|min:0',
            'defense' => 'sometimes|integer|min:0',
            'online_exp_per_5s' => 'sometimes|integer|min:0',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Update level name if level changed
        if (isset($validated['level'])) {
            $levelName = \App\Models\LevelName::getNameWithTierForLevel($validated['level']);
            if ($levelName) {
                $validated['level_name'] = $levelName;
            }
        }

        $user->update($validated);

        return $this->sendResponse($user, 'User updated successfully.');
    }

    /**
     * Change user password
     */
    public function changeUserPassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return $this->sendResponse(null, 'Password changed successfully.');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }

        if ($user->role === 'admin') {
            return $this->sendError('Cannot delete admin user.', [], 403);
        }

        $user->delete();

        return $this->sendResponse(null, 'User deleted successfully.');
    }

    /**
     * Get all level names
     */
    public function levels()
    {
        $levels = LevelName::orderBy('level_min', 'asc')->get();

        return $this->sendResponse($levels, 'Levels retrieved successfully.');
    }

    /**
     * Get all level XP requirements
     */
    public function levelXpRequirements()
    {
        $requirements = LevelXpRequirement::orderBy('level', 'asc')->get();

        return $this->sendResponse($requirements, 'Level XP requirements retrieved successfully.');
    }

    /**
     * Create or update level name
     */
    public function updateLevel(Request $request, $id = null)
    {
        if ($id) {
            $level = LevelName::find($id);
            if (!$level) {
                return $this->sendError('Level not found.', [], 404);
            }
        } else {
            $level = new LevelName();
        }

        $validated = $request->validate([
            'level_min' => 'required|integer|min:1',
            'level_max' => 'required|integer|min:1|gte:level_min',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $level->fill($validated);
        $level->save();

        return $this->sendResponse($level, $id ? 'Level updated successfully.' : 'Level created successfully.');
    }

    /**
     * Create or update level XP requirement
     */
    public function updateLevelXpRequirement(Request $request, $id = null)
    {
        if ($id) {
            $requirement = LevelXpRequirement::find($id);
            if (!$requirement) {
                return $this->sendError('Level XP requirement not found.', [], 404);
            }
        } else {
            $requirement = new LevelXpRequirement();
        }

        $validated = $request->validate([
            'level' => 'required|integer|min:1|unique:level_xp_requirements,level,' . $id,
            'xp_required' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $requirement->fill($validated);
        $requirement->save();

        return $this->sendResponse($requirement, $id ? 'Level XP requirement updated successfully.' : 'Level XP requirement created successfully.');
    }

    /**
     * Delete level name
     */
    public function deleteLevel($id)
    {
        $level = LevelName::find($id);

        if (!$level) {
            return $this->sendError('Level not found.', [], 404);
        }

        $level->delete();

        return $this->sendResponse(null, 'Level deleted successfully.');
    }

    /**
     * Delete level XP requirement
     */
    public function deleteLevelXpRequirement($id)
    {
        $requirement = LevelXpRequirement::find($id);

        if (!$requirement) {
            return $this->sendError('Level XP requirement not found.', [], 404);
        }

        $requirement->delete();

        return $this->sendResponse(null, 'Level XP requirement deleted successfully.');
    }

    /**
     * Get all items
     */
    public function items(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = Item::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->sendResponse($items, 'Items retrieved successfully.');
    }

    /**
     * Create or update item
     */
    public function updateItem(Request $request, $id = null)
    {
        if ($id) {
            $item = Item::find($id);
            if (!$item) {
                return $this->sendError('Item not found.', [], 404);
            }
        } else {
            $item = new Item();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'price' => 'required|integer|min:0',
            'rarity' => 'sometimes|string',
            'grade' => 'sometimes|integer|min:1',
            'slot' => 'nullable|string|in:weapon,armor,accessory',
            'effects' => 'nullable|array',
        ]);

        // Handle effects as JSON if it's an array
        if (isset($validated['effects']) && is_array($validated['effects'])) {
            $validated['effects'] = json_encode($validated['effects']);
        }

        $item->fill($validated);
        $item->save();

        // Reload to get proper JSON effects
        $item->refresh();
        if ($item->effects) {
            $item->effects = is_string($item->effects) ? json_decode($item->effects, true) : $item->effects;
        }

        return $this->sendResponse($item, $id ? 'Item updated successfully.' : 'Item created successfully.');
    }

    /**
     * Delete item
     */
    public function deleteItem($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return $this->sendError('Item not found.', [], 404);
        }

        $item->delete();

        return $this->sendResponse(null, 'Item deleted successfully.');
    }

    /**
     * Get all tasks (admin can see all users' tasks)
     */
    public function tasks(Request $request)
    {
        $perPage = $request->input('per_page', 50);
        $userId = $request->input('user_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Task::with(['user', 'schedule']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->sendResponse($tasks, 'Tasks retrieved successfully.');
    }

    /**
     * Get user tasks
     */
    public function getUserTasks($userId)
    {
        $tasks = Task::where('user_id', $userId)
            ->with(['schedule'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse($tasks, 'User tasks retrieved successfully.');
    }

    /**
     * Get all schedules (admin can see all users' schedules)
     */
    public function schedules(Request $request)
    {
        $perPage = $request->input('per_page', 50);
        $userId = $request->input('user_id');
        $search = $request->input('search');

        $query = Schedule::with(['user']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->sendResponse($schedules, 'Schedules retrieved successfully.');
    }

    /**
     * Get user schedules
     */
    public function getUserSchedules(Request $request, $userId)
    {
        $perPage = $request->input('per_page', 50);
        $search = $request->input('search');

        $query = Schedule::where('user_id', $userId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->sendResponse($schedules, 'User schedules retrieved successfully.');
    }

    /**
     * Get all game rates
     */
    public function gameRates()
    {
        $rates = GameRate::orderBy('order')->get();

        return $this->sendResponse($rates, 'Game rates retrieved successfully.');
    }

    /**
     * Create or update game rate
     */
    public function updateGameRate(Request $request, $id = null)
    {
        if ($id) {
            $rate = GameRate::find($id);
            if (!$rate) {
                return $this->sendError('Game rate not found.', [], 404);
            }
        } else {
            $rate = new GameRate();
        }

        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:game_rates,key,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|in:percentage,multiplier,fixed',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $rate->fill($validated);
        $rate->save();

        return $this->sendResponse($rate, $id ? 'Game rate updated successfully.' : 'Game rate created successfully.');
    }

    /**
     * Delete game rate
     */
    public function deleteGameRate($id)
    {
        $rate = GameRate::find($id);

        if (!$rate) {
            return $this->sendError('Game rate not found.', [], 404);
        }

        $rate->delete();

        return $this->sendResponse(null, 'Game rate deleted successfully.');
    }

    /**
     * Get all monsters
     */
    public function monsters()
    {
        $monsters = Monster::orderBy('level')->get();

        return $this->sendResponse($monsters, 'Monsters retrieved successfully.');
    }

    /**
     * Create or update monster
     */
    public function updateMonster(Request $request, $id = null)
    {
        if ($id) {
            $monster = Monster::find($id);
            if (!$monster) {
                return $this->sendError('Monster not found.', [], 404);
            }
        } else {
            $monster = new Monster();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1',
            'hp' => 'required|integer|min:1',
            'max_hp' => 'required|integer|min:1',
            'attack' => 'required|integer|min:0',
            'defense' => 'required|integer|min:0',
            'xp_reward' => 'required|integer|min:0',
            'currency_reward' => 'required|integer|min:0',
            'drop_items' => 'nullable|array',
            'rarity' => 'required|integer|min:1|max:5',
            'is_boss' => 'boolean',
            'critical_rate' => 'nullable|numeric|min:0|max:1',
            'critical_damage_multiplier' => 'nullable|numeric|min:1|max:10',
            'dodge_rate' => 'nullable|numeric|min:0|max:1',
            'drop_rate_multiplier' => 'nullable|numeric|min:0|max:10',
            'rare_drop_rate' => 'nullable|numeric|min:0|max:1',
            'encounter_rate' => 'nullable|numeric|min:0|max:1',
            'status_effect_resistance' => 'nullable|numeric|min:0|max:1',
        ]);

        $monster->fill($validated);
        $monster->save();

        return $this->sendResponse($monster, $id ? 'Monster updated successfully.' : 'Monster created successfully.');
    }

    /**
     * Delete monster
     */
    public function deleteMonster($id)
    {
        $monster = Monster::find($id);

        if (!$monster) {
            return $this->sendError('Monster not found.', [], 404);
        }

        $monster->delete();

        return $this->sendResponse(null, 'Monster deleted successfully.');
    }

    /**
     * Get all NPCs
     */
    public function npcs()
    {
        if (!Schema::hasTable('npcs')) {
            return $this->sendResponse([], 'NPCs feature is not available.');
        }

        $npcs = NPC::orderBy('level_required')->get();

        return $this->sendResponse($npcs, 'NPCs retrieved successfully.');
    }

    /**
     * Create or update NPC
     */
    public function updateNPC(Request $request, $id = null)
    {
        if (!Schema::hasTable('npcs')) {
            return $this->sendError('NPCs feature is not available.', [], 404);
        }

        if ($id) {
            $npc = NPC::find($id);
            if (!$npc) {
                return $this->sendError('NPC not found.', [], 404);
            }
        } else {
            $npc = new NPC();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'type' => 'required|string|in:merchant,quest_giver,trainer,guard',
            'dialogue' => 'nullable|array',
            'level_required' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $npc->fill($validated);
        $npc->save();

        return $this->sendResponse($npc, $id ? 'NPC updated successfully.' : 'NPC created successfully.');
    }

    /**
     * Delete NPC
     */
    public function deleteNPC($id)
    {
        if (!Schema::hasTable('npcs')) {
            return $this->sendError('NPCs feature is not available.', [], 404);
        }

        $npc = NPC::find($id);

        if (!$npc) {
            return $this->sendError('NPC not found.', [], 404);
        }

        $npc->delete();

        return $this->sendResponse(null, 'NPC deleted successfully.');
    }

    /**
     * Get all story rules
     */
    public function storyRules()
    {
        $rules = StoryRule::with(['story', 'targetCharacter'])
            ->orderBy('order')
            ->get();

        return $this->sendResponse($rules, 'Story rules retrieved successfully.');
    }

    /**
     * Create or update story rule
     */
    public function updateStoryRule(Request $request, $id = null)
    {
        if ($id) {
            $rule = StoryRule::find($id);
            if (!$rule) {
                return $this->sendError('Story rule not found.', [], 404);
            }
        } else {
            $rule = new StoryRule();
        }

        $validated = $request->validate([
            'story_id' => 'nullable|exists:stories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|string|in:kill_monster,complete_task,complete_quest,level_up',
            'trigger_conditions' => 'nullable|array',
            'required_count' => 'required|integer|min:1',
            'target_character_id' => 'nullable|exists:story_characters,id',
            'relationship_value_change' => 'required|integer|min:-100|max:100',
            'relationship_type_change' => 'nullable|string|in:neutral,ally,enemy,rival,friend,master',
            'relationship_threshold' => 'required|integer|min:-100|max:100',
            'has_penalty' => 'boolean',
            'penalty_effects' => 'nullable|array',
            'penalty_due_days' => 'nullable|integer|min:1',
            'is_cumulative' => 'boolean',
            'reset_on_complete' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $rule->fill($validated);
        $rule->save();

        return $this->sendResponse($rule->load(['story', 'targetCharacter']), $id ? 'Story rule updated successfully.' : 'Story rule created successfully.');
    }

    /**
     * Delete story rule
     */
    public function deleteStoryRule($id)
    {
        $rule = StoryRule::find($id);

        if (!$rule) {
            return $this->sendError('Story rule not found.', [], 404);
        }

        $rule->delete();

        return $this->sendResponse(null, 'Story rule deleted successfully.');
    }
}

