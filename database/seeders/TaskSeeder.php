<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use Illuminate\Support\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class TaskSeeder extends Seeder
{
    public function run(Faker $faker)
    {
        // Generiamo 20 task casuali
        for ($i = 0; $i < 20; $i++) {
            $user = User::inRandomOrder()->first();
            $category = Category::inRandomOrder()->first();
            $priority = Priority::inRandomOrder()->first();
            $status = Status::inRandomOrder()->first();

            $newTask = Task::create([
                'name' => $this->generateRandomTaskName(),
                'description' => $this->generateRandomDescription(),
                'deadline' => now()->addDays(rand(1, 30)), // Scadenza casuale tra 1 e 30 giorni
                'estimated_time' => rand(60, 240), // Tempo stimato casuale tra 60 e 240 minuti
                'effective_time' => rand(30, 180), // Tempo effettivo casuale tra 30 e 180 minuti
                'user_id' => $user->id,
                'category_id' => $category->id,
                'priority_id' => $priority->id,
                'status_id' => $status->id,
            ]);


            // Ottieni la data di oggi
            $today = Carbon::today();

            // Calcola l'inizio della settimana (lunedì) e la fine della settimana (domenica)
            $startOfWeek = $today->startOfWeek(); // Lunedì
            $endOfWeek = $today->endOfWeek(); // Domenica

            // Genera una data casuale in questa settimana, incluso un orario casuale
            $randomTimestampThisWeek = rand($startOfWeek->timestamp, $endOfWeek->timestamp);

            // Aggiungi un orario casuale tra 00:00:00 e 23:59:59
            $randomHour = rand(0, 23);
            $randomMinute = rand(0, 59);
            $randomSecond = rand(0, 59);

            // Crea una data casuale con l'orario
            $randomDateThisWeek = Carbon::createFromTimestamp($randomTimestampThisWeek)
                ->setTime($randomHour, $randomMinute, $randomSecond);

            switch ($newTask->status_id) {
                case '3':
                    $newTask->started_at = $randomDateThisWeek;
                    $newTask->ended_at = (clone $newTask->started_at)->addMinutes(rand(15, 240)); // Assicurati di clonare per non modificare la data di inizio
                    $newTask->save();
                    break;
                case '2':
                case '4':
                    $newTask->started_at = $faker->dateTimeThisMonth();
                    $newTask->save();
                    break;
            }
        }
    }

    // Generiamo nomi di task casuali
    private function generateRandomTaskName()
    {
        $taskNames = [
            'Preparare Presentazione',
            'Scrivere Post sul Blog',
            'Risolvere Bug sul Sito Web',
            'Progettare Nuovo Logo',
            'Revisionare Codice',
            'Incontro con il Cliente',
            'Workshop di Team',
            'Scrivere Manuale Utente',
            'Ricercare Nuove Tendenze di Mercato',
            'Pianificare Strategia di Marketing',
            'Organizzare Evento Aziendale',
            'Aggiornare Sito Web',
            'Condurre Interviste con i Clienti',
            'Sviluppare Applicazione Mobile',
            'Analizzare Dati di Vendita',
            'Formazione per il Team',
            'Ottimizzare SEO del Sito',
            'Gestire Campagna Pubblicitaria',
            'Preparare Documento di Progetto',
            'Contattare Fornitori'
        ];

        return $taskNames[array_rand($taskNames)];
    }

    // Generiamo descrizioni di task casuali
    private function generateRandomDescription()
    {
        $descriptions = [
            'Preparare tutte le diapositive necessarie per la prossima presentazione al cliente.',
            'Scrivere un post dettagliato sul blog riguardo il rilascio del nuovo prodotto.',
            'Risolvere un bug critico che influisce sulle prestazioni del sito web.',
            'Progettare un logo fresco e moderno per il progetto di rebranding.',
            'Revisionare le ultime modifiche al codice del team di sviluppo.',
            'Tenere un incontro con il cliente per discutere gli aggiornamenti del progetto.',
            'Condurre un workshop per migliorare la collaborazione del team.',
            'Scrivere un manuale utente completo per il nuovo software.',
            'Ricercare le ultime tendenze di mercato per il prossimo trimestre.',
            'Pianificare la strategia di marketing per la nuova campagna.',
            'Organizzare un evento aziendale per i dipendenti.',
            'Aggiornare il sito web con le ultime informazioni.',
            'Condurre interviste con i clienti per raccogliere feedback.',
            'Sviluppare una nuova applicazione mobile per il servizio clienti.',
            'Analizzare i dati di vendita del mese scorso per identificare tendenze.',
            'Fornire formazione al team su nuove tecnologie.',
            'Ottimizzare il sito per il SEO al fine di migliorare la visibilità.',
            'Gestire la campagna pubblicitaria sui social media.',
            'Preparare un documento di progetto per l’approvazione del cliente.',
            'Contattare i fornitori per negoziare condizioni migliori.'
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
