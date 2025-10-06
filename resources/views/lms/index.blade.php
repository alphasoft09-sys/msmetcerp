@extends('layouts.goi-meta')

@push('styles')
    <style>
        /* LMS Specific Styles */
        .lms-hero {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .lms-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .lms-hero .container {
            position: relative;
            z-index: 2;
        }
        
        .lms-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .lms-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .lms-card-img {
            height: 200px;
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #6b7280;
        }
        
        .lms-stats {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 2rem 0;
            margin: 2rem 0;
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .search-input {
            border: none;
            outline: none;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1.1rem;
        }
        
        .search-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
        }
        
        .department-filter {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .featured-content {
            background: #f8fafc;
            padding: 3rem 0;
            margin: 2rem 0;
        }
        
        .content-type-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(220, 38, 38, 0.9);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .faculty-info {
            display: flex;
            align-items: center;
            margin-top: 1rem;
        }
        
        .faculty-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dc2626;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 0.75rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 16px;
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .empty-state::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .empty-state-content {
            position: relative;
            z-index: 2;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
            display: block;
            width: 100%;
            text-align: center;
        }
        
        .empty-state-icon i {
            display: inline-block;
            width: auto;
            height: auto;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .featured-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(45deg, #f59e0b, #f97316);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
        }
        
        .department-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .department-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .department-card:hover::before {
            left: 100%;
        }
        
        .department-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: #3b82f6;
        }
        
        .search-highlight {
            background: linear-gradient(120deg, #fef3cd 0%, #fde68a 100%);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-weight: 600;
        }
        
        /* Filter Sidebar Styles */
        .filter-sidebar {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Search Input Group in Filter Sidebar */
        .search-input-group {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0;
        }
        
        .search-input-group .form-control {
            flex: 1;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
        }
        
        .search-input-group .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .search-input-group .btn {
            border-radius: 8px;
            padding: 0.6rem 0.8rem;
            min-width: 40px;
        }
        
        .filter-section {
            margin-bottom: 2rem;
        }
        
        .filter-section h6 {
            color: #374151;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .departments-scroll-container {
            max-height: 200px;
            overflow-y: auto;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: #f8fafc;
            min-width: 200px;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .departments-scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .departments-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .departments-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .departments-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Enhanced Department Filter Styling */
        .departments-scroll-container .filter-checkbox {
            margin-bottom: 0.5rem;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            transition: all 0.2s ease;
            position: relative;
            min-width: 200px;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
        }
        
        .departments-scroll-container .filter-checkbox:hover {
            border-color: #3b82f6;
            background: #f8fafc;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }
        
        .departments-scroll-container .filter-checkbox-input:checked + label {
            color: #1e40af;
            font-weight: 600;
        }
        
        .departments-scroll-container .filter-checkbox-input:checked ~ .filter-checkbox {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        
        .departments-scroll-container .filter-checkbox label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin: 0;
            cursor: pointer;
            font-size: 0.9rem;
            min-width: 0;
            flex-shrink: 0;
            flex-wrap: nowrap;
        }
        
        .departments-scroll-container .department-name {
            flex: 1;
            font-weight: 500;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: none;
            min-width: 0;
            margin-right: 8px;
        }
        
        .departments-scroll-container .filter-count {
            background: #e5e7eb;
            color: #6b7280;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 24px;
            width: auto;
            text-align: center;
            flex-shrink: 0;
            white-space: nowrap;
        }
        
        .departments-scroll-container .filter-checkbox-input:checked + label .filter-count {
            background: #3b82f6;
            color: #ffffff;
        }
        
        /* Department search container */
        .department-search-container {
            margin-bottom: 0.5rem;
        }
        
        .department-search-container input {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .department-search-container input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        /* Faculty scroll container */
        .faculty-search-container {
            margin-bottom: 0.5rem;
        }
        
        .faculty-search-container input {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .faculty-search-container input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .faculty-scroll-container {
            max-height: 200px;
            overflow-y: auto;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: #f8fafc;
            min-width: 200px;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .faculty-scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .faculty-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .faculty-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .faculty-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Enhanced Faculty Filter Styling */
        .faculty-scroll-container .filter-checkbox {
            margin-bottom: 0.5rem;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            transition: all 0.2s ease;
            position: relative;
            min-width: 200px;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
        }
        
        .faculty-scroll-container .filter-checkbox:hover {
            border-color: #3b82f6;
            background: #f8fafc;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }
        
        .faculty-scroll-container .filter-checkbox-input:checked + label {
            color: #1e40af;
            font-weight: 600;
        }
        
        .faculty-scroll-container .filter-checkbox-input:checked ~ .filter-checkbox {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        
        .faculty-scroll-container .filter-checkbox label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin: 0;
            cursor: pointer;
            font-size: 0.9rem;
            min-width: 0;
            flex-shrink: 0;
            flex-wrap: nowrap;
        }
        
        .faculty-scroll-container .faculty-name {
            flex: 1;
            font-weight: 500;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: none;
            min-width: 0;
            margin-right: 8px;
        }
        
        .faculty-scroll-container .filter-count {
            background: #e5e7eb;
            color: #6b7280;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 24px;
            width: auto;
            text-align: center;
            flex-shrink: 0;
            white-space: nowrap;
        }
        
        .faculty-scroll-container .filter-checkbox-input:checked + label .filter-count {
            background: #3b82f6;
            color: #ffffff;
        }
        
        .filter-checkbox {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .filter-checkbox:hover {
            background: #e5e7eb;
        }
        
        .filter-checkbox input[type="checkbox"] {
            margin-right: 0.75rem;
            transform: scale(1.1);
        }
        
        .filter-checkbox label {
            margin: 0;
            cursor: pointer;
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .filter-count {
            background: #dc2626;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .filter-actions {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .filter-actions .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .content-area {
            min-height: 600px;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 12px;
        }
        
        .loading-spinner {
            width: 3rem;
            height: 3rem;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #dc2626;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .filter-mobile-toggle {
            display: none;
            margin-bottom: 1rem;
        }
        
        .filter-mobile-toggle .btn {
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            border-radius: 8px;
        }
        
        /* Responsive Design Enhancements */
        @media (max-width: 768px) {
            .lms-hero {
                padding: 2rem 0;
            }
            
            .lms-hero h1 {
                font-size: 2rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            /* Department scroll container responsive */
            .departments-scroll-container {
                max-height: 150px;
            }
            
            .departments-scroll-container .filter-checkbox {
                padding: 0.5rem;
            }
            
            .departments-scroll-container .department-name {
                max-width: none;
                font-size: 0.85rem;
                min-width: 0;
                margin-right: 6px;
            }
            
            .departments-scroll-container .filter-count {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
                width: auto;
                min-width: 20px;
                flex-shrink: 0;
                white-space: nowrap;
            }
            
            /* Faculty scroll container responsive */
            .faculty-scroll-container {
                max-height: 150px;
            }
            
            .faculty-scroll-container .filter-checkbox {
                padding: 0.5rem;
            }
            
            .faculty-scroll-container .faculty-name {
                max-width: none;
                font-size: 0.85rem;
                min-width: 0;
                margin-right: 6px;
            }
            
            .faculty-scroll-container .filter-count {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
                width: auto;
                min-width: 20px;
                flex-shrink: 0;
                white-space: nowrap;
            }
            
            .filter-sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 85%;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease;
                overflow-y: auto;
                background: white;
                padding: 1.5rem;
                border-radius: 0;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }
            
            .filter-sidebar.show {
                left: 0;
            }
            
            .filter-section {
                margin-bottom: 1.5rem;
            }
            
            .filter-section h6 {
                font-size: 1rem;
                margin-bottom: 0.8rem;
            }
            
            .search-input-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .search-input-group .btn {
                width: 100%;
            }
            
            .departments-scroll-container {
                max-height: 150px;
                padding-right: 6px;
            }
            
            .filter-checkbox {
                margin-bottom: 0.8rem;
            }
            
            .filter-checkbox label {
                font-size: 0.9rem;
                padding: 0.6rem 0.8rem;
            }
            
            .filter-actions {
                margin-top: 1.5rem;
            }
            
            .filter-actions .btn {
                width: 100%;
                margin-bottom: 0.8rem;
                padding: 0.8rem 1rem;
                font-size: 0.95rem;
            }
            
            .filter-mobile-toggle {
                display: block;
            }
            
            .content-area {
                margin-top: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .filter-sidebar {
                width: 90%;
                padding: 1rem;
            }
            
            .filter-section {
                margin-bottom: 1.2rem;
            }
            
            .filter-section h6 {
                font-size: 0.95rem;
                margin-bottom: 0.6rem;
            }
            
            .departments-scroll-container {
                max-height: 120px;
                padding-right: 4px;
            }
            
            .filter-checkbox {
                margin-bottom: 0.6rem;
            }
            
            .filter-checkbox label {
                font-size: 0.85rem;
                padding: 0.5rem 0.6rem;
            }
            
            .filter-actions {
                margin-top: 1.2rem;
            }
            
            .filter-actions .btn {
                padding: 0.7rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .filter-mobile-toggle .btn {
                padding: 0.7rem 0.8rem;
                font-size: 0.9rem;
            }
            
            /* Search functionality moved to Filter Sidebar */
            
            /* Content Cards Mobile */
            .content-card {
                margin-bottom: 1rem;
            }
            
            .content-card-header {
                padding: 1rem;
            }
            
            .content-card-body {
                padding: 1rem;
            }
            
            .content-card-title {
                font-size: 1rem;
                margin-bottom: 0.8rem;
            }
            
            .content-preview {
                font-size: 0.85rem;
                margin-bottom: 1rem;
            }
            
            .content-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }
            
            .faculty-info {
                flex-direction: row;
                align-items: center;
            }
            
            .faculty-avatar {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
            
            /* Simple Cards Mobile */
            .simple-content-card {
                border-radius: 10px;
                box-shadow: 0 1px 6px rgba(0, 0, 0, 0.08);
            }
            
            .card-header-simple {
                padding: 0.8rem 1rem;
            }
            
            .card-body-simple {
                padding: 1rem;
            }
            
            .card-title-simple {
                font-size: 1rem;
                margin-bottom: 0.6rem;
            }
            
            .card-description-simple {
                margin-bottom: 0.8rem;
            }
            
            .description-simple {
                font-size: 0.85rem;
                line-height: 1.4;
            }
            
            .faculty-section-simple {
                margin-bottom: 0.8rem;
            }
            
            .faculty-info-simple {
                gap: 0.6rem;
            }
            
            .faculty-avatar-simple {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
                border-radius: 6px;
            }
            
            .faculty-name-simple {
                font-size: 0.85rem;
            }
            
            .btn-simple {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
                border-radius: 6px;
            }
        }
        
        @media (max-width: 576px) {
            .lms-hero h1 {
                font-size: 1.5rem;
            }
            
            .lms-hero .lead {
                font-size: 1rem;
            }
            
            .stats-card h3 {
                font-size: 1.5rem;
            }
            
            .empty-state {
                padding: 2rem 1rem;
                margin: 1rem 0;
                border-radius: 12px;
            }
            
            .empty-state-icon {
                font-size: 3rem;
                margin-bottom: 1rem;
                text-align: center;
            }
            
            .empty-state-content h3 {
                font-size: 1.3rem;
                margin-bottom: 0.8rem;
            }
            
            .empty-state-content p {
                font-size: 0.95rem;
                margin-bottom: 1.5rem;
                line-height: 1.4;
            }
            
            .empty-state-content .btn {
                padding: 0.7rem 1.5rem;
                font-size: 0.9rem;
            }
            
            .filter-sidebar {
                width: 90%;
            }
        }
        
        /* Content Card Enhancements */
        .content-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
        }
        
        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .content-card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .content-card-body {
            padding: 1.5rem;
        }
        
        .content-meta {
            display: flex;
            align-items: center;
            justify-content-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .faculty-info {
            display: flex;
            align-items: center;
        }
        
        .faculty-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            margin-right: 0.75rem;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .status-approved {
            background: #dcfce7;
            color: #166534;
        }
        
        .content-preview {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Simple Content Card Styles */
        .simple-content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .simple-content-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-header-simple {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .department-tag {
            background: #3b82f6;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        
        .card-date-simple {
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .card-body-simple {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .card-title-simple {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        
        .card-description-simple {
            margin-bottom: 1rem;
            flex: 1;
        }
        
        .description-simple {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }
        
        .faculty-section-simple {
            margin-bottom: 1rem;
        }
        
        .faculty-info-simple {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .faculty-avatar-simple {
            width: 40px;
            height: 40px;
            background: #f59e0b;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }
        
        .faculty-details-simple {
            display: flex;
            flex-direction: column;
        }
        
        .faculty-label-simple {
            font-size: 0.75rem;
            color: #9ca3af;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .faculty-name-simple {
            font-size: 0.9rem;
            color: #374151;
            font-weight: 600;
        }
        
        .card-action-simple {
            margin-top: auto;
        }
        
        .btn-simple {
            background: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
        }
        
        .btn-simple:hover {
            background: #2563eb;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        /* Modern Hero Section Styles */
        .modern-hero {
            position: relative;
            min-height: 70vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
        }
        
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        
        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: patternMove 20s ease-in-out infinite;
        }
        
        .hero-gradient {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(102, 126, 234, 0.9) 0%, 
                rgba(118, 75, 162, 0.9) 50%, 
                rgba(255, 107, 107, 0.8) 100%);
        }
        
        @keyframes patternMove {
            0%, 100% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(-10px) translateY(-5px); }
            50% { transform: translateX(5px) translateY(-10px); }
            75% { transform: translateX(-5px) translateY(5px); }
        }
        
        .hero-content {
            position: relative;
            z-index: 3;
            color: white;
        }
        
        .hero-visual {
            position: relative;
            z-index: 3;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-badge {
            display: inline-block;
        }
        
        .badge-text {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            color: white;
        }
        
        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
        }
        
        .title-highlight {
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-description {
            font-size: 1.1rem;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1.5rem;
        }
        
        .hero-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .floating-cards {
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        .floating-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 1rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: float 6s ease-in-out infinite;
        }
        
        .card-1 {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .card-2 {
            top: 30%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .card-3 {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.8rem;
            color: white;
            font-size: 1.2rem;
        }
        
        .card-content h6 {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .card-content p {
            color: #718096;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .hero-stats {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 1rem 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-item {
            text-align: center;
            color: white;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.3rem;
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .hero-scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            text-align: center;
            color: white;
            animation: bounce 2s infinite;
        }
        
        .scroll-text {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }
        
        .scroll-arrow {
            font-size: 1.5rem;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }
        
        .min-vh-60 {
            min-height: 50vh;
        }
        
        /* Search functionality moved to Filter Sidebar */

        /* Load More Button Styles */
        #loadMoreContainer {
            display: block !important;
            margin-top: 2rem;
        }
        
        #loadMoreBtn {
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 2px solid #0d6efd;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            display: inline-block !important;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
            font-size: 1rem;
            min-width: 200px;
        }
        
        #loadMoreBtn:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.4);
        }
        
        #loadMoreBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        }
        
        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .spinner-border {
            width: 2rem;
            height: 2rem;
        }
        
        /* Responsive Load More Button */
        @media (max-width: 768px) {
            #loadMoreBtn {
                padding: 0.7rem 1.5rem;
                font-size: 0.9rem;
                min-width: 180px;
            }
            
            #loadMoreContainer {
                margin-top: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            #loadMoreBtn {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
                min-width: 160px;
            }
            
            #loadMoreContainer {
                margin-top: 1rem;
            }
        }
        
        /* Responsive Design */
        
        @media (max-width: 768px) {
            .modern-hero {
                min-height: 60vh;
                padding: 1rem 0;
            }
            
            .hero-title {
                font-size: 2.2rem;
                margin-bottom: 0.8rem;
            }
            
            .hero-description {
                font-size: 1rem;
                margin-bottom: 1.2rem;
            }
            
            .hero-features {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.6rem;
                margin-bottom: 1.2rem;
            }
            
            .feature-item {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }
            
            .hero-actions {
                flex-direction: column;
                gap: 0.8rem;
            }
            
            .hero-actions .btn {
                width: 100%;
                padding: 0.8rem 1rem;
                font-size: 0.95rem;
            }
            
            .hero-visual {
                height: 300px;
                margin-top: 1.5rem;
            }
            
            .floating-card {
                padding: 0.8rem;
                border-radius: 12px;
            }
            
            .card-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
                margin-bottom: 0.6rem;
            }
            
            .card-content h6 {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }
            
            .card-content p {
                font-size: 0.75rem;
            }
            
            .hero-stats {
                position: relative;
                bottom: auto;
                left: auto;
                transform: none;
                margin-top: 1.5rem;
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
                padding: 0.8rem 1rem;
            }
            
            .stat-number {
                font-size: 1.8rem;
                margin-bottom: 0.2rem;
            }
            
            .stat-label {
                font-size: 0.8rem;
            }
            
            .hero-scroll-indicator {
                bottom: 1rem;
            }
            
            .scroll-text {
                font-size: 0.8rem;
            }
            
            .scroll-arrow {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 576px) {
            .modern-hero {
                min-height: 55vh;
                padding: 0.8rem 0;
            }
            
            .hero-title {
                font-size: 1.8rem;
                margin-bottom: 0.6rem;
            }
            
            .hero-description {
                font-size: 0.95rem;
                margin-bottom: 1rem;
                line-height: 1.4;
            }
            
            .hero-features {
                gap: 0.5rem;
                margin-bottom: 1rem;
            }
            
            .feature-item {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            
            .hero-actions {
                gap: 0.6rem;
            }
            
            .hero-actions .btn {
                padding: 0.7rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .hero-visual {
                height: 250px;
                margin-top: 1rem;
            }
            
            .floating-card {
                padding: 0.6rem;
                border-radius: 10px;
            }
            
            .card-icon {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
                margin-bottom: 0.4rem;
            }
            
            .card-content h6 {
                font-size: 0.8rem;
                margin-bottom: 0.2rem;
            }
            
            .card-content p {
                font-size: 0.7rem;
            }
            
            .hero-stats {
                margin-top: 1rem;
                gap: 0.8rem;
                padding: 0.6rem 0.8rem;
            }
            
            .stat-number {
                font-size: 1.6rem;
                margin-bottom: 0.1rem;
            }
            
            .stat-label {
                font-size: 0.75rem;
            }
            
            .hero-scroll-indicator {
                bottom: 0.8rem;
            }
            
            .scroll-text {
                font-size: 0.75rem;
            }
            
            .scroll-arrow {
                font-size: 1rem;
            }
            
            /* Search functionality moved to Filter Sidebar */
            
            /* Empty State Small Mobile */
            .empty-state {
                padding: 1.5rem 0.8rem;
                margin: 0.8rem 0;
                border-radius: 10px;
            }
            
            .empty-state-icon {
                font-size: 2.5rem;
                margin-bottom: 0.8rem;
                text-align: center;
            }
            
            .empty-state-content h3 {
                font-size: 1.1rem;
                margin-bottom: 0.6rem;
            }
            
            .empty-state-content p {
                font-size: 0.85rem;
                margin-bottom: 1.2rem;
                line-height: 1.3;
            }
            
            .empty-state-content .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }
            
            /* Simple Cards Small Mobile */
            .simple-content-card {
                border-radius: 8px;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            }
            
            .card-header-simple {
                padding: 0.6rem 0.8rem;
            }
            
            .card-body-simple {
                padding: 0.8rem;
            }
            
            .card-title-simple {
                font-size: 0.95rem;
                margin-bottom: 0.5rem;
            }
            
            .card-description-simple {
                margin-bottom: 0.6rem;
            }
            
            .description-simple {
                font-size: 0.8rem;
                line-height: 1.3;
            }
            
            .faculty-section-simple {
                margin-bottom: 0.6rem;
            }
            
            .faculty-avatar-simple {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
                border-radius: 5px;
            }
            
            .faculty-name-simple {
                font-size: 0.8rem;
            }
            
            .btn-simple {
                padding: 0.5rem 0.8rem;
                font-size: 0.8rem;
                border-radius: 5px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Modern LMS Hero Section -->
    <section class="modern-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
            <div class="hero-gradient"></div>
        </div>
        
        <div class="container position-relative">
            <div class="row align-items-center min-vh-60">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="hero-badge mb-2">
                            <span class="badge-text">
                                <i class="fas fa-certificate me-2"></i>
                                Government of India Initiative
                            </span>
                        </div>
                        
                        <h1 class="hero-title mb-3">
                            <span class="title-highlight">Educational</span>
                            <br>Learning Management System
                        </h1>
                        
                        <p class="hero-description mb-3">
                            Discover comprehensive educational content, cutting-edge courses, research papers, 
                            and learning materials designed to empower students and educators across India.
                        </p>
                        
                        <div class="hero-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <span>NEP 2020 Compliant</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <span>Quality Assured Content</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <span>Expert Faculty</span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-visual">
                        <div class="floating-cards">
                            <div class="floating-card card-1">
                                <div class="card-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="card-content">
                                    <h6>Interactive Learning</h6>
                                    <p>Engaging content</p>
                                </div>
                            </div>
                            
                            <div class="floating-card card-2">
                                <div class="card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="card-content">
                                    <h6>Expert Faculty</h6>
                                    <p>Industry professionals</p>
                                </div>
                            </div>
                            
                            <div class="floating-card card-3">
                                <div class="card-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="card-content">
                                    <h6>Certified Courses</h6>
                                    <p>Government approved</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number">{{ $stats['total_courses'] }}</div>
                                <div class="stat-label">Courses</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">{{ $stats['total_departments'] }}</div>
                                <div class="stat-label">Departments</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">{{ $stats['total_faculty'] }}</div>
                                <div class="stat-label">Faculty</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="hero-scroll-indicator">
            <div class="scroll-text">Scroll to explore</div>
            <div class="scroll-arrow">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </section>

    <!-- Search functionality moved to Filter Sidebar -->

    <!-- Main Content Area -->
    <div class="container mt-4">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 col-md-4">
                <!-- Mobile Filter Toggle -->
                <div class="filter-mobile-toggle">
                    <button class="btn btn-outline-primary" onclick="toggleMobileFilter()">
                        <i class="fas fa-filter me-2"></i>Filter Options
                    </button>
                </div>
                
                <div class="filter-sidebar" id="filterSidebar">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Filter Options</h5>
                        <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="toggleMobileFilter()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="filterForm">
                        <!-- Search Box -->
                        <div class="filter-section">
                            <!-- <h6><i class="fas fa-search me-2"></i>Search</h6> -->
                            <div class="search-input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="searchInput"
                                       name="search" 
                                       value="{{ $urlParams['search'] ?? '' }}"
                                       placeholder="Search courses, topics, or keywords...">
                                <button class="btn btn-primary btn-sm" type="button" onclick="applyFilters()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Department Filter -->
                        <div class="filter-section">
                            <h6><i class="fas fa-building me-2"></i>Departments</h6>
                            <div class="department-search-container mb-2">
                                <input type="text" 
                                       id="departmentSearchInput" 
                                       class="form-control form-control-sm" 
                                       placeholder="Search departments..." 
                                       onkeyup="filterDepartments()">
                            </div>
                            <div class="departments-scroll-container">
                                @foreach($departments as $department)
                                    <div class="filter-checkbox department-item" data-department-name="{{ strtolower($department->department_name) }}">
                                        <input type="checkbox" 
                                               id="dept_{{ $loop->index }}" 
                                               name="departments[]" 
                                               value="{{ $department->department_name }}"
                                               {{ in_array($department->department_name, (array)($urlParams['departments'] ?? [])) ? 'checked' : '' }}
                                               class="filter-checkbox-input">
                                        <label for="dept_{{ $loop->index }}" title="{{ $department->department_name }}">
                                            <span class="department-name">{{ Str::limit($department->department_name, 20) }}</span>
                                            <span class="filter-count">{{ $department->count }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Faculty Filter -->
                        <div class="filter-section">
                            <h6><i class="fas fa-chalkboard-teacher me-2"></i>Faculty</h6>
                            <div class="faculty-search-container mb-2">
                                <input type="text" 
                                       id="facultySearchInput" 
                                       class="form-control form-control-sm" 
                                       placeholder="Search faculty..." 
                                       onkeyup="filterFaculty()">
                            </div>
                            <div class="faculty-scroll-container">
                                @foreach($faculty as $fac)
                                    <div class="filter-checkbox faculty-item" data-faculty-name="{{ strtolower($fac->faculty_name) }}">
                                        <input type="checkbox" 
                                               id="fac_{{ $loop->index }}" 
                                               name="faculty[]" 
                                               value="{{ $fac->faculty_display_name }}"
                                               {{ in_array($fac->faculty_display_name, (array)($urlParams['faculty'] ?? [])) ? 'checked' : '' }}
                                               class="filter-checkbox-input">
                                        <label for="fac_{{ $loop->index }}" title="{{ $fac->faculty_name }}">
                                            <span class="faculty-name">{{ Str::limit($fac->faculty_name, 20) }}</span>
                                            <span class="filter-count">{{ $fac->count }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearAllFilters()">
                                <i class="fas fa-times me-2"></i>Clear All
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9 col-md-8">
                <div class="content-area position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            <span id="contentTitle">
                                @if(request('search'))
                                    Search Results for "{{ request('search') }}"
                                @elseif(request('departments') || request('faculty'))
                                    Filtered Content
                                @else
                                    All Educational Content
                                @endif
                            </span>
                        </h3>
                        <div class="text-muted">
                            <span id="resultCount">{{ $lmsSites->count() }}</span> results found
                        </div>
                    </div>

                    <!-- Loading Overlay -->
                    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <p class="mt-2 text-muted">Loading content...</p>
                        </div>
                    </div>

                    <!-- Content Grid -->
                    <div id="contentGrid">
                        @include('lms.partials.content-grid', ['lmsSites' => $lmsSites])
                    </div>

                    <!-- Load More Button (Created Dynamically) -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Mobile filter toggle
    function toggleMobileFilter() {
        const sidebar = document.getElementById('filterSidebar');
        sidebar.classList.toggle('show');
    }

    // Clear all filters
    function clearAllFilters() {
        // Clear all checkboxes
        document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        
        // Clear search inputs
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Clear faculty search input and reset faculty list
        const facultySearchInput = document.getElementById('facultySearchInput');
        if (facultySearchInput) {
            facultySearchInput.value = '';
        }
        
        // Reset faculty list to show all faculty
        const facultyItems = document.querySelectorAll('.faculty-item');
        facultyItems.forEach(item => {
            item.style.display = 'block';
        });
        
        // Clear department search input and reset department list
        const departmentSearchInput = document.getElementById('departmentSearchInput');
        if (departmentSearchInput) {
            departmentSearchInput.value = '';
        }
        
        // Reset department list to show all departments
        const departmentItems = document.querySelectorAll('.department-item');
        departmentItems.forEach(item => {
            item.style.display = 'block';
        });
        
        // Reset Load More state
        currentPage = 2;
        isLoadingMore = false;
        
        // Clear URL parameters
        window.history.pushState({}, '', window.location.pathname);
        
        // Force refresh of content with no filters
        console.log('ClearAllFilters - Forcing content refresh');
        applyFilters();
        
        // Ensure layout is maintained after clearing
        setTimeout(() => {
            maintainFilterLayout();
        }, 200);
    }

    // Show loading overlay
    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    // Hide loading overlay
    function hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    // Create or update Load More button dynamically
    function updateLoadMoreButton(hasMorePages = false, showButton = true) {
        let loadMoreContainer = document.getElementById('loadMoreContainer');
        
        // Create container if it doesn't exist
        if (!loadMoreContainer) {
            loadMoreContainer = document.createElement('div');
            loadMoreContainer.id = 'loadMoreContainer';
            loadMoreContainer.className = 'text-center mt-4';
            
            // Insert after content grid
            const contentGrid = document.getElementById('contentGrid');
            if (contentGrid && contentGrid.parentNode) {
                contentGrid.parentNode.insertBefore(loadMoreContainer, contentGrid.nextSibling);
            }
        }
        
        if (showButton && hasMorePages) {
            // Show Load More button
            loadMoreContainer.innerHTML = `
                <button class="btn btn-primary btn-lg" id="loadMoreBtn">
                    <i class="fas fa-plus-circle me-2"></i>
                    Load More Content
                </button>
                <div class="loading-spinner mt-3" id="loadMoreSpinner" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading more content...</p>
                </div>
            `;
            loadMoreContainer.style.display = 'block';
        } else if (showButton && !hasMorePages) {
            // Show "No more content" message with better styling
            loadMoreContainer.innerHTML = `
                <div class="alert alert-info mt-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>All content loaded!</strong><br>
                    <small>You've reached the end of available content.</small>
                </div>
            `;
            loadMoreContainer.style.display = 'block';
        } else {
            // Hide container
            loadMoreContainer.style.display = 'none';
        }
    }

    // Reset Load More button to initial state
    function resetLoadMoreButton() {
        // Don't hide the button, just reset the state
        // The button will be updated by applyFilters() based on actual data
        currentPage = 2;
        isLoadingMore = false;
    }

    // Unified function to handle all filter operations
    function applyFilters(isLoadMore = false) {
        if (!isLoadMore) {
            showLoading();
            // Reset Load More functionality for new filter application
            currentPage = 2;
            isLoadingMore = false;
        }
        
        console.log('ApplyFilters called - isLoadMore:', isLoadMore, 'currentPage:', currentPage);
        
        const formData = new FormData(document.getElementById('filterForm'));
        
        // Add page number for Load More
        if (isLoadMore) {
            formData.append('page', currentPage);
            console.log('Load More: Sending page', currentPage);
        }
        
        // Add CSRF token
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const route = isLoadMore ? '{{ route("public.lms.load-more") }}' : '{{ route("public.lms.index") }}';
        console.log('Making AJAX request to:', route, 'with data:', Object.fromEntries(formData.entries()));
        
        fetch(route, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('AJAX Response received:', {
                isLoadMore: isLoadMore,
                success: data.success,
                hasMorePages: data.hasMorePages,
                htmlLength: data.html ? data.html.length : 0,
                data: data
            });
            
            if (data.success) {
                if (isLoadMore) {
                    // Handle Load More response
                    console.log('Handling Load More response');
                    handleLoadMoreResponse(data);
                } else {
                    // Handle regular filter response
                    console.log('Handling regular filter response');
                    handleFilterResponse(data);
                }
            } else {
                console.error('Request failed:', data.message);
                if (isLoadMore) {
                    // Reset loading state for Load More on failure
                    isLoadingMore = false;
                    const loadMoreBtn = document.getElementById('loadMoreBtn');
                    const loadMoreSpinner = document.getElementById('loadMoreSpinner');
                    if (loadMoreBtn) loadMoreBtn.style.display = 'block';
                    if (loadMoreSpinner) loadMoreSpinner.style.display = 'none';
                    alert('Failed to load more content. Please try again.');
                } else {
                    alert('Failed to apply filters. Please try again.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (isLoadMore) {
                // Reset loading state for Load More on error
                isLoadingMore = false;
                const loadMoreBtn = document.getElementById('loadMoreBtn');
                const loadMoreSpinner = document.getElementById('loadMoreSpinner');
                if (loadMoreBtn) loadMoreBtn.style.display = 'block';
                if (loadMoreSpinner) loadMoreSpinner.style.display = 'none';
                alert('Error loading more content. Please try again.');
            } else {
                alert('An error occurred while applying filters. Please try again.');
            }
        })
        .finally(() => {
            if (!isLoadMore) {
                hideLoading();
            }
        });
    }

    // Handle regular filter response
    function handleFilterResponse(data) {
        // Update content grid - replace the entire grid content
        const contentGrid = document.getElementById('contentGrid');
        contentGrid.innerHTML = data.html;
        
        // Update result count
        const resultCount = document.querySelectorAll('.simple-content-card').length;
        document.getElementById('resultCount').textContent = resultCount;
        
        // Update content title
        updateContentTitle();
        
        // Update URL with current filters
        updateURL();
        
        // Close mobile filter if open
        document.getElementById('filterSidebar').classList.remove('show');
        
        // Ensure filter layout is maintained after dynamic content loading
        setTimeout(() => {
            maintainFilterLayout();
        }, 100);
        
        // Handle Load More button visibility
        console.log('Filter Response - Load More Data:', {
            hasMorePages: data.hasMorePages,
            currentPage: data.currentPage,
            totalPages: data.totalPages,
            totalItems: data.totalItems
        });
        updateLoadMoreButton(data.hasMorePages, true);
        console.log('Load More button updated, hasMorePages:', data.hasMorePages);
    }

    // Handle Load More response
    function handleLoadMoreResponse(data) {
        console.log('handleLoadMoreResponse called with:', {
            hasHtml: !!data.html,
            htmlLength: data.html ? data.html.length : 0,
            htmlContent: data.html ? data.html.substring(0, 200) + '...' : 'No HTML',
            hasMorePages: data.hasMorePages,
            noMoreContent: data.noMoreContent
        });
        
        // Reset loading state first
        isLoadingMore = false;
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const loadMoreSpinner = document.getElementById('loadMoreSpinner');
        if (loadMoreSpinner) loadMoreSpinner.style.display = 'none';
        
        // Check if we have new content to append
        if (data.html && data.html.trim() !== '') {
            console.log('Processing HTML content for Load More');
            console.log('Full HTML received:', data.html);
            
            const contentGrid = document.getElementById('contentGrid');
            console.log('Content Grid found:', !!contentGrid);
            
            if (contentGrid) {
                // Method 1: Try to find existing row and append columns
                const existingRow = contentGrid.querySelector('.row');
                console.log('Existing row found:', !!existingRow);
                
                if (existingRow) {
                    // Parse the new HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    const newRow = tempDiv.querySelector('.row');
                    
                    if (newRow) {
                        // Get all columns from the new row and append to existing row
                        const newColumns = newRow.querySelectorAll('.col-xl-4');
                        console.log('Found new columns to append:', newColumns.length);
                        
                        if (newColumns.length > 0) {
                            newColumns.forEach((col, index) => {
                                console.log(`Appending column ${index + 1}`);
                                // Clone the node to ensure it's properly attached
                                const clonedCol = col.cloneNode(true);
                                existingRow.appendChild(clonedCol);
                                
                                // Ensure the new card is visible
                                clonedCol.style.display = 'block';
                                clonedCol.style.opacity = '1';
                            });
                            
                            // Update result count
                            const resultCount = document.getElementById('resultCount');
                            if (resultCount) {
                                const currentCount = parseInt(resultCount.textContent);
                                resultCount.textContent = currentCount + newColumns.length;
                                console.log('Updated result count from', currentCount, 'to', resultCount.textContent);
                            }
                            
                            console.log('Successfully added', newColumns.length, 'new cards');
                        } else {
                            console.log('No columns found in new row, trying fallback');
                            // Fallback: append the entire HTML
                            contentGrid.insertAdjacentHTML('beforeend', data.html);
                        }
                    } else {
                        console.log('No new row found, appending HTML directly');
                        contentGrid.insertAdjacentHTML('beforeend', data.html);
                    }
                } else {
                    console.log('No existing row found, appending HTML directly');
                    contentGrid.insertAdjacentHTML('beforeend', data.html);
                }
            } else {
                console.error('Content grid not found!');
            }
        } else {
            console.log('No HTML content received for Load More');
            console.log('Data received:', data);
        }
        
        // Force a reflow to ensure the new content is visible
        if (contentGrid) {
            contentGrid.offsetHeight; // Trigger reflow
            console.log('Triggered reflow for content grid');
            
            // Scroll to show the new content
            const newCards = contentGrid.querySelectorAll('.simple-content-card');
            if (newCards.length > 0) {
                const lastCard = newCards[newCards.length - 1];
                lastCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                console.log('Scrolled to show new content');
            }
        }
        
        // Update page counter for next Load More request
        currentPage = data.nextPage || (currentPage + 1);
        console.log('Updated currentPage to:', currentPage);
        
        // Update Load More button based on remaining pages or no more content
        if (data.noMoreContent || !data.hasMorePages) {
            updateLoadMoreButton(false, true); // Show "No more content available"
        } else {
            updateLoadMoreButton(data.hasMorePages, true); // Show Load More button
        }
        
        // Debug log
        console.log('Load More Response:', {
            success: data.success,
            hasMorePages: data.hasMorePages,
            nextPage: data.nextPage,
            currentPage: data.currentPage,
            totalPages: data.totalPages,
            totalItems: data.totalItems,
            itemsOnCurrentPage: data.itemsOnCurrentPage,
            noMoreContent: data.noMoreContent
        });
    }

    // Update content title based on active filters
    function updateContentTitle() {
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value : '';
        const selectedDepartments = Array.from(document.querySelectorAll('input[name="departments[]"]:checked')).map(cb => cb.value);
        const selectedFaculty = Array.from(document.querySelectorAll('input[name="faculty[]"]:checked')).map(cb => cb.value);
        
        let title = 'All Educational Content';
        
        if (searchValue) {
            title = `Search Results for "${searchValue}"`;
        } else if (selectedDepartments.length > 0 || selectedFaculty.length > 0) {
            title = 'Filtered Content';
        }
        
        const contentTitle = document.getElementById('contentTitle');
        if (contentTitle) {
            contentTitle.textContent = title;
        }
    }

    // Update URL with current filter parameters
    function updateURL() {
        const url = new URL(window.location);
        const params = new URLSearchParams();
        
        // Get current filter values
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value : '';
        const selectedDepartments = Array.from(document.querySelectorAll('input[name="departments[]"]:checked')).map(cb => cb.value);
        const selectedFaculty = Array.from(document.querySelectorAll('input[name="faculty[]"]:checked')).map(cb => cb.value);
        
        // Add parameters to URL
        if (searchValue) {
            params.set('search', searchValue);
        }
        
        if (selectedDepartments.length > 0) {
            selectedDepartments.forEach(dept => {
                params.append('departments[]', dept);
            });
        }
        
        if (selectedFaculty.length > 0) {
            selectedFaculty.forEach(fac => {
                params.append('faculty[]', fac);
            });
        }
        
        // Update URL without page reload
        const newUrl = params.toString() ? `${url.pathname}?${params.toString()}` : url.pathname;
        window.history.pushState({}, '', newUrl);
    }

    // Parse URL parameters and set form values
    function parseURLParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        console.log('Parsing URL parameters:', window.location.search);
        
        // Set search value
        const searchValue = urlParams.get('search');
        if (searchValue) {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = searchValue;
                console.log('Set search value:', searchValue);
            }
        }
        
        // Set department checkboxes
        const departments = urlParams.getAll('departments[]');
        console.log('Department parameters:', departments);
        document.querySelectorAll('input[name="departments[]"]').forEach(checkbox => {
            const isChecked = departments.includes(checkbox.value);
            checkbox.checked = isChecked;
            console.log(`Department ${checkbox.value}: ${isChecked ? 'checked' : 'unchecked'}`);
        });
        
        // Set faculty checkboxes
        const faculty = urlParams.getAll('faculty[]');
        console.log('Faculty parameters:', faculty);
        document.querySelectorAll('input[name="faculty[]"]').forEach(checkbox => {
            const isChecked = faculty.includes(checkbox.value);
            checkbox.checked = isChecked;
            console.log(`Faculty ${checkbox.value}: ${isChecked ? 'checked' : 'unchecked'}`);
        });
        
    }

        // Initialize form with URL parameters
        function initializeForm() {
            console.log('Initializing form with URL parameters');
            parseURLParameters();
            
            // Apply filters if URL has parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.toString()) {
                console.log('URL has parameters, applying filters');
                applyFilters();
            } else {
                console.log('No URL parameters, showing default content');
                // Show Load More button on initial load
                updateLoadMoreButton({{ $lmsSites->hasMorePages() ? 'true' : 'false' }}, true);
            }
        }
        

        // Faculty search functionality
        function filterFaculty() {
            const searchInput = document.getElementById('facultySearchInput');
            const searchTerm = searchInput.value.toLowerCase();
            const facultyItems = document.querySelectorAll('.faculty-item');
            
            facultyItems.forEach(item => {
                const facultyName = item.getAttribute('data-faculty-name');
                if (facultyName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Department search functionality
        function filterDepartments() {
            const searchInput = document.getElementById('departmentSearchInput');
            const searchTerm = searchInput.value.toLowerCase();
            const departmentItems = document.querySelectorAll('.department-item');
            
            departmentItems.forEach(item => {
                const departmentName = item.getAttribute('data-department-name');
                if (departmentName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Maintain filter layout after dynamic content loading
        function maintainFilterLayout() {
            // Force reflow to ensure proper layout calculation
            const departmentContainer = document.querySelector('.departments-scroll-container');
            const facultyContainer = document.querySelector('.faculty-scroll-container');
            
            if (departmentContainer) {
                departmentContainer.style.width = '100%';
                departmentContainer.style.minWidth = '200px';
            }
            
            if (facultyContainer) {
                facultyContainer.style.width = '100%';
                facultyContainer.style.minWidth = '200px';
            }
            
            // Ensure all filter checkboxes maintain proper width
            const filterCheckboxes = document.querySelectorAll('.departments-scroll-container .filter-checkbox, .faculty-scroll-container .filter-checkbox');
            filterCheckboxes.forEach(checkbox => {
                checkbox.style.width = '100%';
                checkbox.style.minWidth = '200px';
                checkbox.style.boxSizing = 'border-box';
            });
        }

        // Global variables for Load More functionality
        let currentPage = 2;
        let isLoadingMore = false;

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form with URL parameters
        initializeForm();

        // Filter form submission
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });
        }

        // Search form submission (now part of filter form)
        // No separate search form needed since search is integrated into filter form

        // Use event delegation for Load More button (works even after DOM changes)
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'loadMoreBtn') {
                if (isLoadingMore) return;
                loadMoreContent();
            }
        });

        function loadMoreContent() {
            if (isLoadingMore) {
                console.log('Load More already in progress, ignoring click');
                return;
            }
            
            console.log('LoadMoreContent called - currentPage:', currentPage);
            
            isLoadingMore = true;
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const loadMoreSpinner = document.getElementById('loadMoreSpinner');
            
            // Show loading state
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            if (loadMoreSpinner) loadMoreSpinner.style.display = 'flex';
            
            console.log('Calling applyFilters with isLoadMore=true, currentPage:', currentPage);
            // Use the unified applyFilters function with isLoadMore = true
            applyFilters(true);
        }

        // Auto-apply filters on checkbox change (with debounce)
        let filterTimeout;
        document.querySelectorAll('#filterForm input[type="checkbox"], #filterForm select').forEach(element => {
            element.addEventListener('change', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(applyFilters, 500);
            });
        });

        // Auto-apply filters on date change
        document.querySelectorAll('#filterForm input[type="date"]').forEach(element => {
            element.addEventListener('change', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(applyFilters, 500);
            });
        });

        // Auto-apply filters on search input (with debounce)
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 1000);
            });
        }

        // Close mobile filter when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('filterSidebar');
            const toggle = document.querySelector('.filter-mobile-toggle button');
            
            if (window.innerWidth <= 768 && 
                sidebar && toggle &&
                !sidebar.contains(e.target) && 
                !toggle.contains(e.target) && 
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('filterSidebar');
                if (sidebar) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Handle browser back/forward navigation
        window.addEventListener('popstate', function(e) {
            console.log('Browser navigation detected, parsing URL parameters');
            parseURLParameters();
            applyFilters();
        });

        // Also handle URL changes from other sources
        const originalPushState = history.pushState;
        const originalReplaceState = history.replaceState;
        
        history.pushState = function() {
            originalPushState.apply(history, arguments);
            setTimeout(() => {
                console.log('URL changed via pushState, parsing parameters');
                parseURLParameters();
            }, 100);
        };
        
        history.replaceState = function() {
            originalReplaceState.apply(history, arguments);
            setTimeout(() => {
                console.log('URL changed via replaceState, parsing parameters');
                parseURLParameters();
            }, 100);
        };
    });

    // Font size adjustment
    document.querySelectorAll('[data-lang="font-size"]').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const body = document.body;
            const currentSize = parseFloat(getComputedStyle(body).fontSize);
            
            if (action === 'increase') {
                body.style.fontSize = (currentSize + 2) + 'px';
            } else if (action === 'decrease') {
                body.style.fontSize = (currentSize - 2) + 'px';
            }
        });
    });

    // Search input enhancement (now part of filter form)
    // Search input is handled by the filter form submission
</script>
@endpush

