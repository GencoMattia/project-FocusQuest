<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewTaskRequest;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Task;
use App\Models\Time_Interval;
use App\Models\Pause;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class ApiTaskController extends Controller
{
    public function show($id)
    {

        $user_id = auth()->user()->id;
        $task = Task::where('user_id', '=',  $user_id)
            ->where("id", $id)
            ->with(['priority', 'status', 'category'])
            ->first();

        if ($task) {
            return response()->json([
                'message' => 'success',
                'task' => $task
            ]);
        } else {
            return response()->json([
                'message' => 'Task non trovata',
            ], 404);
        }
    }

    public function store(CreateNewTaskRequest $request)
    {
        $data = $request->validated();
        $newTask = Task::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'estimated_time' => $data['estimated_time'],
            'user_id' => auth()->id(),
            'category_id' => $data['category_id'],
            'priority_id' => $data['priority_id'],
            'status_id' => 1,
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $newTask]);
    }

    public function getUserTask()
    {
        $authenticated_user_id = auth()->user()->id;
        $tasks = Task::with('priority')
            ->with('status')
            ->with('category')
            ->where('user_id', $authenticated_user_id)
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found for this user.'], 404);
        }

        return response()->json($tasks);
    }

    public function getFormData()
    {
        $priorities = Priority::all();
        $categories = Category::all();
        $statuses = Status::all();

        return response()->json([
            "message" => "success",
            "data" => [
                "priorities" => $priorities,
                "categories" => $categories,
                "statuses" => $statuses
            ]
        ]);
    }

    public function modifyTaskStatus(Request $request)
    {
        $data = $request->validate([
            'status_id' => 'required|integer|exists:statuses,id',
            'task_id' => 'required|integer|exists:tasks,id'
        ]);

        $task = Task::findOrFail($data['task_id']);
        $status = $data['status_id'];

        //! AVVIO
        if ($status == 2) {

            //? se esistono pause
            if ($task->pauses()->exists()) {

                // Inizializza l'array vuoto
                $total_pauses_array = [];

                // Recupera tutte le pause concluse
                $pauses = Pause::where('task_id', $task->id)
                    ->whereNotNull('ended_at')
                    ->get();

                // Se ci sono pause concluse, assegna l'array
                if ($pauses->count() > 0) {
                    $total_pauses_array = $pauses;
                }

                //**Ritrovo l'ultima pausa associata alla task (quindi ancora aperta) */
                $last_pause = Pause::where('task_id', $task->id)
                    ->whereNull('ended_at')
                    ->first();

                //**Completo il campo ended_at dell-ultima pausa */
                $last_pause->ended_at = now();
                $last_pause->save();

                //**Calcolo la durata in minuti della pausa appena conclusa */
                $last_pause_created_at = $last_pause->created_at;
                $pause_interval = Carbon::parse($last_pause->ended_at)->diffInMinutes($last_pause_created_at);

                //**Aggiorno lo status della task **IN PROGRESS** */
                $task->status_id = $status;
                $task->save();

                return response()->json([
                    'message' => 'Task riavviata',
                    'task' => $task,
                    'last_pause' => $last_pause,
                    'last_pause_duration_minutes' => $pause_interval,
                    'total_pauses_array' => $total_pauses_array
                ]);
            }

            //? se NON esistono pause
            else {

                //**Compilo il campo started_at della Task, rendendola attiva */
                $task->started_at = now();
                $task->status_id = $status;
                $task->save();

                return response()->json([
                    'message' => 'Task avviata',
                    'task' => $task,
                ]);
            }
        }


        //! PAUSA
        if ($status == 4) {

            //**Aggiorno lo stato della task **IN PAUSA** */
            $task->status_id = $status;
            $task->save();

            //**Creo un'istanza del modello Pause */
            $pause_data = [
                'task_id' => $task->id,
            ];
            $new_pause = Pause::create($pause_data);

            return response()->json([
                'message' => 'Task in pausa!',
                'task' => $task,
                'pause' => $new_pause
            ]);
        }

        //! COMPLETATA
        if ($status == 3) {
            //**Aggiorno lo stato della task e l'id dello stato **COMPLETATA** */
            $task->ended_at = now();
            $task->status_id = $status;
            $task->save();


            //*Dichiaro la variabile effective_time */
            $effective_time = 0;

            if ($task->started_at) {

                //**! SE ESISTONO PAUSE */
                if ($task->pauses()->exists()) {

                    $total_pauses = [];
                    // Recupero tutte le pause concluse (quelle con ended_at compilato)
                    $pauses = Pause::where('task_id', $task->id)
                        ->whereNotNull('ended_at')
                        ->get();

                    foreach ($pauses as $pause) {
                        // Calcola la durata della pausa in minuti
                        $pause_duration = Carbon::parse($pause->ended_at)->diffInMinutes($pause->created_at);
                        // Aggiungi la durata all'array
                        array_push($total_pauses, intval($pause_duration));
                    }

                    // Ora puoi calcolare la somma totale delle durate delle pause
                    $total_pause_time = array_sum($total_pauses);
                    // $total_pause_count = count($total_pauses);
                    $total_time_with_pauses = Carbon::parse($task->ended_at)->diffInMinutes($task->started_at);

                    // Calcolo del tempo effettivo
                    $effective_time = intval($total_time_with_pauses) - $total_pause_time;

                    if ($task->estimated_time > $effective_time) {
                        $earned_time = $task->estimated_time - $effective_time;
                        $effective_time_message = 'Ci hai messo di meno di quanto pensavi! ' . 'Hai guadagnato ' . $earned_time . ' minuti';
                    } else {
                        $earned_time = $effective_time - $task->estimated_time;
                        $effective_time_message = 'Ci hai messo di più di quanto pensavi! ' . 'Ci hai messo ' . $earned_time . ' minuti in più';
                    }

                    $task->status_id = $status;
                    $task->effective_time = $effective_time;
                    $task->save();


                    return response()->json([
                        'message' => 'Task completata con pause',
                        // 'number_of_pauses' => $total_pause_count,
                        'total_task_time' => $this->formatTime($total_time_with_pauses),
                        'effective_task_time' => $this->formatTime($effective_time),
                        'total_pause_time' => $this->formatTime($total_pause_time),
                        'effective_time_message' => $effective_time_message,
                        'total_pauses' => $total_pauses,
                        'task' => $task
                    ]);
                }
                //**! SE NON ESISTONO PAUSE */
                else {
                    //**Calcolo la durata in minuti della task e la parso come un intero */
                    $unparsed_effective_time = Carbon::parse($task->started_at)->diffInMinutes(Carbon::parse($task->ended_at));
                    $effective_time = intval($unparsed_effective_time);

                    //**Diversi messaggi in base alla differenza fra tempo stimato e tempo effettivo */
                    if ($task->estimated_time > $effective_time) {
                        $earned_time = $task->estimated_time - $effective_time;
                        $effective_time_message = 'Ci hai messo di meno di quanto pensavi! ' . 'Hai guadagnato ' . $earned_time . ' minuti';
                    } else {
                        $earned_time = $effective_time - $task->estimated_time;
                        $effective_time_message = 'Ci hai messo di più di quanto pensavi! ' . 'Ci hai messo ' . $earned_time . ' minuti in più';
                    }

                    //**Aggiorno lo stato della task */
                    $task->status_id = $status;
                    $task->effective_time = $effective_time;
                    $task->save();

                    return response()->json([
                        'message' => 'Task completata senza pause',
                        'effective_task_time' => $effective_time,
                        'effective_time_message' => $effective_time_message,
                        'task' => $task
                    ]);
                }
            }
        }

        $task->status_id = $status;
        $task->save();

        return response()->json([
            'message' => 'status updated successfully',
            'task' => $task
        ]);
    }

    //*! UTILITIES *//
    private function formatTime($minutes) {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        return "{$hours} ore {$remainingMinutes} minuti";
    }
}
