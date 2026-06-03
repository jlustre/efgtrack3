import './page-chrome';
import Alpine from 'alpinejs';
import taskManagement from './task-management';
import cfmManagement from './cfm-management';
import profileTableFilter from './profile-table-filter';
import orgChartBoard from './org-chart-board';
import downlineHierarchyTable from './downline-hierarchy-table';

window.Alpine = Alpine;

Alpine.data('taskManagement', taskManagement);
Alpine.data('cfmManagement', cfmManagement);
Alpine.data('profileTableFilter', profileTableFilter);
Alpine.data('orgChartBoard', orgChartBoard);
Alpine.data('downlineHierarchyTable', downlineHierarchyTable);

Alpine.start();
