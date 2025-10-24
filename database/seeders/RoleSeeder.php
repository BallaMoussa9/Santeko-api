<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run()
    {
       $roles = [
    [
        'name' => 'admin',
        'display_name' => 'Administrateur',
        'description' => 'Gère l’administration globale du système',
        'type' => 'admin',
    ],
    [
        'name' => 'super_admin',
        'display_name' => 'Super Administrateur',
        'description' => 'Accès total à toutes les fonctionnalités, y compris les droits systèmes',
        'type' => 'super_admin',
    ],
    [
        'name' => 'doctor',
        'display_name' => 'Médecin',
        'description' => 'Prend en charge les diagnostics et les prescriptions médicales',
        'type' => 'doctor',
    ],
    [
        'name' => 'nurse',
        'display_name' => 'Infirmier/Infirmière',
        'description' => 'Assiste les médecins et s’occupe des soins aux patients',
        'type' => 'nurse',
    ],
    [
        'name' => 'infirmier',
        'display_name' => 'Infirmier',
        'description' => 'Assure les soins infirmiers',
        'type' => 'infirmier',
    ],
    [
        'name' => 'pharmacist',
        'display_name' => 'Pharmacien',
        'description' => 'Gère les médicaments et les prescriptions pharmaceutiques',
        'type' => 'pharmacist',
    ],
    [
        'name' => 'secretary',
        'display_name' => 'Secrétaire',
        'description' => 'Gère les dossiers, la prise de rendez-vous et l’accueil',
        'type' => 'secretary',
    ],
    [
        'name' => 'accountant',
        'display_name' => 'Comptable',
        'description' => 'S’occupe de la gestion financière et de la facturation',
        'type' => 'accountant',
    ],
    [
        'name' => 'patient',
        'display_name' => 'Patient',
        'description' => 'Utilisateur recevant les soins médicaux',
        'type' => 'patient',
    ],
    [
        'name' => 'comms_officer',
        'display_name' => 'Chargé de communication',
        'description' => 'Gère la communication interne et externe du centre',
        'type' => 'comms_officer',
    ],
    [
        'name' => 'emergency_doctor',
        'display_name' => 'Médecin urgentiste',
        'description' => 'Gère les urgences médicales et les soins intensifs immédiats',
        'type' => 'emergency_doctor',
    ],
    [
        'name' => 'paramedic',
        'display_name' => 'Ambulancier / Paramédic',
        'description' => 'Assure les premiers soins et le transport des patients',
        'type' => 'paramedic',
    ],
    [
        'name' => 'lab_technician',
        'display_name' => 'Technicien de laboratoire',
        'description' => 'Réalise les analyses et examens en laboratoire',
        'type' => 'lab_technician',
    ],
    [
        'name' => 'lab_supervisor',
        'display_name' => 'Superviseur de laboratoire',
        'description' => 'Supervise les opérations et l’équipe du laboratoire',
        'type' => 'lab_supervisor',
    ],
    [
        'name' => 'lab_assistant',
        'display_name' => 'Assistant de laboratoire',
        'description' => 'Aide à la préparation et à l’exécution des analyses',
        'type' => 'lab_assistant',
    ],
    [
        'name' => 'lab_manager',
        'display_name' => 'Responsable de laboratoire',
        'description' => 'Gère l’organisation, les équipements et les résultats du laboratoire',
        'type' => 'lab_manager',
    ],
    [
        'name' => 'pathologist',
        'display_name' => 'Pathologiste',
        'description' => 'Spécialiste de l’analyse des tissus et des résultats médicaux',
        'type' => 'pathologist', // ✅ corrigé ici
    ],
];


        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                    'type' => $role['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
