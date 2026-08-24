<?php

namespace App\Helpers;

use App\Models\Project;

class ProjectHelper
{
    /**
     * Get project ID by project name
     *
     * @param string $projectName
     * @return int|null
     */
    public static function getProjectIdByName($projectName)
    {
        return Project::where('project_name', $projectName)->value('id');
    }

    /**
     * Get Add2Farm project ID
     *
     * @return int|null
     */
    public static function getAdd2FarmProjectId()
    {
        return self::getProjectIdByName('Add2Farm');
    }

    /**
     * Get project by name
     *
     * @param string $projectName
     * @return \App\Models\Project|null
     */
    public static function getProjectByName($projectName)
    {
        return Project::where('project_name', $projectName)->first();
    }
}
