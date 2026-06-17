import './page-chrome';
import taskManagement from './task-management';
import cfmManagement from './cfm-management';
import profileTableFilter from './profile-table-filter';
import orgChartBoard from './org-chart-board';
import downlineHierarchyTable from './downline-hierarchy-table';
import profilePhotoUpload from './profile-photo-upload';
import prospectActivitiesModal from './prospect-activities-modal';
import genealogyTreePan from './genealogy-tree-pan';
import dashboardStats from './dashboard-stats';
import prospectKanbanBoard from './prospect-kanban-board';

// Register Alpine data on Livewire's Alpine instance (started by @livewireScripts).
document.addEventListener('alpine:init', () => {
    window.Alpine.data('taskManagement', taskManagement);
    window.Alpine.data('cfmManagement', cfmManagement);
    window.Alpine.data('profileTableFilter', profileTableFilter);
    window.Alpine.data('orgChartBoard', orgChartBoard);
    window.Alpine.data('downlineHierarchyTable', downlineHierarchyTable);
    window.Alpine.data('profilePhotoUpload', profilePhotoUpload);
    window.Alpine.data('prospectActivitiesModal', prospectActivitiesModal);
    window.Alpine.data('genealogyTreePan', genealogyTreePan);
    window.Alpine.data('dashboardStats', dashboardStats);
    window.Alpine.data('prospectKanbanBoard', prospectKanbanBoard);
});
