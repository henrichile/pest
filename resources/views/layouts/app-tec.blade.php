<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist - {{ $service->id }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        h1 {
            color: #1a472a;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: bold;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .service-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .service-info h3 {
            color: #1a472a;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
        }
        
        .info-value {
            color: #666;
        }
        
        .progress-box {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .progress-box h4 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: #1a472a;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .stage-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .stage-box h4 {
            color: #1a472a;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .stage-box h4 .icon {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .stage-title {
            color: #1a472a;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stage-instruction {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #1a472a;
            outline: none;
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .checkbox-item:hover {
            border-color: #1a472a;
            background: #f8f9fa;
        }
        
        .checkbox-item.checked {
            border-color: #1a472a;
            background: #e8f5e8;
        }
        
        .checkbox-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #1a472a;
        }
        
        .checkbox-item label {
            margin: 0;
            cursor: pointer;
            flex: 1;
            font-size: 16px;
            color: #333;
        }
        
        .radio-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .radio-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .radio-item:hover {
            border-color: #1a472a;
            background: #f8f9fa;
        }
        
        .radio-item.checked {
            border-color: #1a472a;
            background: #e8f5e8;
        }
        
        .radio-item input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #1a472a;
        }
        
        .radio-item label {
            margin: 0;
            cursor: pointer;
            flex: 1;
            font-size: 16px;
            color: #333;
        }
        
        .buttons-container {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .next-button {
            background: #1a472a;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .next-button:hover {
            background: #2d5016;
            transform: translateY(-1px);
        }
        
        .next-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .next-button .arrow {
            margin-left: 6px;
            font-size: 14px;
        }
        
        .back-button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .back-button:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .back-button .arrow {
            margin-right: 6px;
            font-size: 14px;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1a472a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .stage-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        
        .stage-indicator span {
            padding: 5px 10px;
            border-radius: 15px;
            background: #f0f0f0;
        }
        
        .stage-indicator span.active {
            background: #1a472a;
            color: white;
        }
        
        .stage-indicator a {
            text-decoration: none;
            color: inherit;
            display: inline-block;
        }
        
        .stage-indicator a:hover {
            opacity: 0.8;
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
        
        .stage-indicator span.completed {
            background: #28a745;
            color: white;
        }
    </style>
    @yield('css')
</head>
<body>
    <div class="container">
        @yield('content')
    </div>

@yield('scripts')
</body>
</html>