<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class LinkStudentsToParents extends Command
{
    protected $signature = 'csndr:link-students';
    protected $description = 'Lie automatiquement tous les élèves orphelins à des parents existants';

    public function handle()
    {
        $this->info('=== Lancement de la liaison automatique parent-enfant ===');

        $parents = User::where('role', 'parent')->get();
        if ($parents->isEmpty()) {
            $this->error('Aucun parent trouvé. Impossible de lier les élèves.');
            return 1;
        }

        $this->info('Parents trouvés: ' . $parents->count());

        $orphanStudents = User::where('role', 'eleve')->whereNull('parent_id')->get();
        if ($orphanStudents->isEmpty()) {
            $this->info('✅ Tous les élèves sont déjà liés à un parent.');
            return 0;
        }

        $this->info('Élèves orphelins trouvés: ' . $orphanStudents->count());

        $parentCount = $parents->count();
        $parentIndex = 0;

        foreach ($orphanStudents as $student) {
            $parent = $parents[$parentIndex];
            $student->parent_id = $parent->id;
            $student->save();

            $this->line("- Élève '{$student->prenom} {$student->nom}' lié au parent '{$parent->prenom} {$parent->nom}'.");

            // Round-robin assignment
            $parentIndex = ($parentIndex + 1) % $parentCount;
        }

        $this->info('\n🎉 Liaison terminée avec succès!');
        return 0;
    }
}
