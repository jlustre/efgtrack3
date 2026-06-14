import './page-chrome';
import Alpine from 'alpinejs';
import taskManagement from './task-management';
import cfmManagement from './cfm-management';
import profileTableFilter from './profile-table-filter';
import orgChartBoard from './org-chart-board';
import downlineHierarchyTable from './downline-hierarchy-table';
import genealogyTreePan from './genealogy-tree-pan';
import dashboardStats from './dashboard-stats';
import prospectKanbanBoard from './prospect-kanban-board';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('taskManagement', taskManagement);
    Alpine.data('cfmManagement', cfmManagement);
    Alpine.data('profileTableFilter', profileTableFilter);
    Alpine.data('orgChartBoard', orgChartBoard);
    Alpine.data('downlineHierarchyTable', downlineHierarchyTable);
    Alpine.data('genealogyTreePan', genealogyTreePan);
    Alpine.data('dashboardStats', dashboardStats);
    Alpine.data('prospectKanbanBoard', prospectKanbanBoard);
});
