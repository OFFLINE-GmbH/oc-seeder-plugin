<?php

namespace OFFLINE\Seeder\Models;


use Model;

class SeederSettings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'offline_seeder_settings';
    public $settingsFields = 'fields.yaml';
}