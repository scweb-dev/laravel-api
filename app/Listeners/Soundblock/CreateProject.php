<?php

namespace App\Listeners\Soundblock;

use Util;
use Client;
use Constant;
use App\Models\Core\Auth\AuthModel;
use App\Events\Soundblock\CreateProject as CreateProjectEvent;
use App\Services\{Core\Auth\AuthGroup, Core\Auth\AuthPermission};

class CreateProject {
    /** @var AuthGroup */
    protected AuthGroup $authGroupService;
    /** @var AuthPermission */
    protected AuthPermission $authPermService;

    /**
     * Create the event listener.
     * @param AuthGroup $authGroupService
     * @param AuthPermission $authPermService
     * @return void
     */
    public function __construct(AuthGroup $authGroupService, AuthPermission $authPermService) {
        $this->authGroupService = $authGroupService;
        $this->authPermService = $authPermService;
    }

    /**
     * Handle the event.
     *
     * @param CreateProjectEvent $event
     * @return void
     */
    public function handle(CreateProjectEvent $event) {
        $objAuth = AuthModel::where("auth_name", "App.Soundblock")->first();
        $objProject = $event->objProject;
        $arrUsers = $event->arrUsers;

        $arrGroup = [];

        $arrGroup["auth_id"] = $objAuth->auth_id;
        $arrGroup["auth_uuid"] = $objAuth->auth_uuid;
        $arrGroup["group_name"] = Util::makeGroupName($objAuth, "project", $objProject);
        $arrGroup["group_memo"] = Util::makeGroupMemo($objAuth, "project", $objProject);
        $arrGroup["flag_critical"] = true;
        // create the project group for this project.
//        $objProjectPermission = Constant::project_level_permissions();
        $soundblockProjectPermissions = $this->authPermService->findAllWhere(["App.Soundblock.Project.%"], "name");
        $soundblockProjectPermissions = $soundblockProjectPermissions->merge(Constant::project_level_permissions());
        $objAuthGroup = $this->authGroupService->create($arrGroup, true);

        $this->authPermService->attachGroupPermissions($soundblockProjectPermissions, $objAuthGroup);

        // Add the user to project group.
        foreach ($arrUsers as $strRole => $objUser) {
            $this->authGroupService->addUserToGroup($objUser, $objAuthGroup, Client::app());

            if ($strRole == "Owner") {
                $this->authPermService->attachUserPermissions($soundblockProjectPermissions, $objUser, $objAuthGroup);
            } else {
                $objAccountProjects = $objProject->account->projects;

                if ($objAccountProjects->count() == 2) {
                    $objLatestProject = $objAccountProjects->first();
                } else {
                    $intCount = $objAccountProjects->count();
                    $objLatestProject = $objAccountProjects[$intCount - 2];
                }

                $objProjectGroup = $this->authGroupService->findByProject($objLatestProject);
                $objUserLatestProjectPermissions = $this->authPermService->findAllUserPermissionsByGroup($objProjectGroup, $objUser);

                foreach ($objUserLatestProjectPermissions as $objUserLatestProjectPermission) {
                    $this->authPermService->attachUserPermission($objUserLatestProjectPermission, $objUser, $objAuthGroup, $objUserLatestProjectPermission->pivot->permission_value);
                }
            }
        }
    }
}
