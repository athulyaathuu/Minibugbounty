<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Bug Bounty - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #0a0e27;
            --secondary-bg: #151b3d;
            --accent-color: #00d9ff;
            --text-primary: #e8eaf6;
            --text-secondary: #b0b8d4;
            --success: #00ff88;
            --warning: #ffd93d;
            --danger: #ff3d71;
            --critical: #c70039;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--primary-bg);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .navbar {
            background: var(--secondary-bg);
            border-bottom: 2px solid var(--accent-color);
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 20px rgba(0, 217, 255, 0.1);
        }

        .navbar-brand {
            color: var(--accent-color);
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            font-size: 1.8rem;
        }

        .navbar-brand:hover {
            color: var(--accent-color);
        }

        .dropdown-toggle {
            background: transparent;
            border: 1px solid var(--accent-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .dropdown-toggle:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
        }

        .dropdown-menu {
            background: var(--secondary-bg);
            border: 1px solid var(--accent-color);
        }

        .dropdown-item {
            color: var(--text-primary);
        }

        .dropdown-item:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
        }

        .sidebar {
            position: fixed;
            top: 72px;
            left: 0;
            width: 260px;
            height: calc(100vh - 72px);
            background: var(--secondary-bg);
            border-right: 2px solid rgba(0, 217, 255, 0.2);
            padding: 2rem 0;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
        }

        .sidebar-nav li {
            margin: 0.5rem 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a i {
            margin-right: 1rem;
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            color: var(--accent-color);
            background: rgba(0, 217, 255, 0.1);
            border-left-color: var(--accent-color);
        }

        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: calc(100vh - 72px);
        }

        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-header h2 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            color: var(--text-secondary);
        }

        .stat-card {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), transparent);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.2);
            border-color: var(--accent-color);
        }

        .stat-card.low::before {
            background: linear-gradient(90deg, var(--success), transparent);
        }

        .stat-card.high::before {
            background: linear-gradient(90deg, var(--warning), transparent);
        }

        .stat-card.critical::before {
            background: linear-gradient(90deg, var(--danger), transparent);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
        }

        .stat-card.low .stat-icon {
            color: var(--success);
        }

        .stat-card.high .stat-icon {
            color: var(--warning);
        }

        .stat-card.critical .stat-icon {
            color: var(--danger);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .chart-container {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-placeholder {
            text-align: center;
            color: var(--text-secondary);
        }

        .chart-placeholder i {
            font-size: 4rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .table-container {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            padding: 2rem;
            overflow-x: auto;
        }

        .table-container h4 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table {
            color: var(--text-primary);
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--primary-bg);
            color: var(--accent-color);
            border-color: rgba(0, 217, 255, 0.2);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            padding: 1rem;
        }

        .table tbody td {
            border-color: rgba(0, 217, 255, 0.1);
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(0, 217, 255, 0.05);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-low {
            background: rgba(0, 255, 136, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .badge-medium {
            background: rgba(255, 217, 61, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .badge-high {
            background: rgba(255, 61, 113, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .badge-critical {
            background: rgba(199, 0, 57, 0.2);
            color: var(--critical);
            border: 1px solid var(--critical);
        }

        .badge-open {
            background: rgba(0, 217, 255, 0.2);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
        }

        .badge-review {
            background: rgba(255, 217, 61, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .badge-fixed {
            background: rgba(0, 255, 136, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .btn-view {
            background: transparent;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }

        .section-title {
            color: var(--text-primary);
            margin: 2rem 0 1.5rem 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--accent-color);
        }
		
		.btn-add {
            background: var(--accent-color);
            color: var(--primary-bg);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-add:hover {
            background: #00b8d4;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 217, 255, 0.4);
        }

        .filter-section {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .filter-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control, .form-select {
            background: var(--primary-bg);
            border: 1px solid rgba(0, 217, 255, 0.3);
            color: var(--text-primary);
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: var(--primary-bg);
            border-color: var(--accent-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 217, 255, 0.25);
        }

        .form-select option {
            background: var(--secondary-bg);
            color: var(--text-primary);
        }

        .btn-filter {
            background: transparent;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
        }

        .btn-reset {
            background: transparent;
            border: 1px solid var(--text-secondary);
            color: var(--text-secondary);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: var(--text-secondary);
            color: var(--primary-bg);
        }

        .vulnerabilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .vuln-card {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .vuln-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .vuln-card.critical::before {
            background: var(--critical);
        }

        .vuln-card.high::before {
            background: var(--danger);
        }

        .vuln-card.medium::before {
            background: var(--warning);
        }

        .vuln-card.low::before {
            background: var(--success);
        }

        .vuln-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.2);
            border-color: var(--accent-color);
        }

        .vuln-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .vuln-id {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .vuln-title {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0.5rem 0;
            line-height: 1.4;
        }

        .vuln-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .vuln-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-low {
            background: rgba(0, 255, 136, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .badge-medium {
            background: rgba(255, 217, 61, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .badge-high {
            background: rgba(255, 61, 113, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .badge-critical {
            background: rgba(199, 0, 57, 0.2);
            color: var(--critical);
            border: 1px solid var(--critical);
        }

        .badge-open {
            background: rgba(0, 217, 255, 0.2);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
        }

        .badge-under-review {
            background: rgba(255, 217, 61, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .badge-fixed {
            background: rgba(0, 255, 136, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .badge-closed {
            background: rgba(176, 184, 212, 0.2);
            color: var(--text-secondary);
            border: 1px solid var(--text-secondary);
        }

        .vuln-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 217, 255, 0.1);
        }

        .reporter-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .reporter-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-bg);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .reporter-details {
            display: flex;
            flex-direction: column;
        }

        .reporter-name {
            color: var(--text-primary);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .report-date {
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        .vuln-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            background: transparent;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-icon:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
        }

        .stats-bar {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-item i {
            font-size: 1.5rem;
        }

        .stat-item.critical i {
            color: var(--critical);
        }

        .stat-item.high i {
            color: var(--danger);
        }

        .stat-item.medium i {
            color: var(--warning);
        }

        .stat-item.low i {
            color: var(--success);
        }

        .stat-text {
            display: flex;
            flex-direction: column;
        }

        .stat-number {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination {
            gap: 0.5rem;
        }

        .page-link {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
            border-color: var(--accent-color);
        }

        .page-item.active .page-link {
            background: var(--accent-color);
            color: var(--primary-bg);
            border-color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .vulnerabilities-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add {
                width: 100%;
                justify-content: center;
            }

            .stats-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }
		
		.custom-table {
            width: 100%;
            color: var(--text-primary);
        }

        .custom-table thead th {
            border-bottom: 2px solid var(--border-color);
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .custom-table tbody td {
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
            vertical-align: middle;
        }

        .custom-table tbody tr {
            transition: all 0.3s ease;
        }

        .custom-table tbody tr:hover {
            background: rgba(0, 212, 255, 0.03);
        }
		
		.logs-container {
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            padding: 2rem;
        }

        .logs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 217, 255, 0.2);
        }

        .logs-header h4 {
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logs-count {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--accent-color), transparent);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 2rem;
        }

        .timeline-marker {
            position: absolute;
            left: -2rem;
            top: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 3px solid var(--accent-color);
            background: var(--primary-bg);
            z-index: 1;
        }

        .timeline-marker.success {
            border-color: var(--success);
        }

        .timeline-marker.warning {
            border-color: var(--warning);
        }

        .timeline-marker.danger {
            border-color: var(--danger);
        }

        .timeline-marker.info {
            border-color: var(--info);
        }

        .log-card {
            background: var(--primary-bg);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 8px;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }

        .log-card:hover {
            border-color: var(--accent-color);
            box-shadow: 0 5px 15px rgba(0, 217, 255, 0.15);
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }

        .log-type {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .log-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .log-icon.success {
            background: rgba(0, 255, 136, 0.1);
            color: var(--success);
        }

        .log-icon.warning {
            background: rgba(255, 217, 61, 0.1);
            color: var(--warning);
        }

        .log-icon.danger {
            background: rgba(255, 61, 113, 0.1);
            color: var(--danger);
        }

        .log-icon.info {
            background: rgba(0, 217, 255, 0.1);
            color: var(--info);
        }

        .log-details h5 {
            color: var(--text-primary);
            font-size: 1rem;
            margin: 0 0 0.25rem 0;
            font-weight: 600;
        }

        .log-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .log-meta span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .log-timestamp {
            font-size: 0.8rem;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .log-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0.75rem 0;
        }

        .log-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(0, 217, 255, 0.1);
            margin-top: 0.75rem;
        }

        .log-user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-bg);
            font-weight: 600;
            font-size: 0.75rem;
        }

        .user-name {
            color: var(--text-primary);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .log-ip {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-family: 'Courier New', monospace;
        }
		.chart-box {
    width: 100%;
    max-width: 320px;   /* control width */
    height: 220px;      /* control height */
    margin: auto;
}
    </style>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-halved"></i>
                Mini Bug Bounty
            </a>
            <div class="dropdown ms-auto">
                <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-shield me-2"></i>Admin
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <aside class="sidebar">
        <ul class="sidebar-nav">
            <li><a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i>Dashboard</a></li>
            <li><a href="vulnerabilities.php"><i class="fas fa-bug"></i>Vulnerabilities</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i>Users</a></li>
            <li><a href="logs.php"><i class="fas fa-history"></i>Activity Logs</a></li>
			<li><a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </aside>