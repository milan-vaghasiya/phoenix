-- 03-11-2025 --
ALTER TABLE `project_master` ADD `work_size` FLOAT NULL DEFAULT NULL AFTER `amount`;

--27-11-2025--
INSERT INTO `sub_menu_master` (`id`, `menu_type`, `sub_menu_seq`, `sub_menu_icon`, `sub_menu_name`, `sub_controller_name`, `menu_id`, `is_report`, `is_approve_req`, `is_system`, `report_id`, `notify_on`, `vou_name_long`, `vou_name_short`, `auto_start_no`, `vou_prefix`, `created_by`, `created_at`, `updated_by`, `updated_at`, `is_delete`) VALUES (NULL, '1', '1', 'icon-Record', 'Site History', 'reports/siteHistoryReport', '18', '1', '0', '0', NULL, '0,0,0', '', '', '1', '', '1', '2025-11-27 21:10:25', '0', '2025-11-27 21:10:25', '0');
