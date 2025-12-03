<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Not Verified</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                
                <h1 class="mt-4 text-2xl font-bold text-gray-900">Domain Not Verified</h1>
                
                <p class="mt-2 text-gray-600">
                    The domain <strong class="text-gray-900">{{ $domain }}</strong> has not been verified yet.
                </p>
                
                <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 text-left">
                    <p class="text-sm text-blue-700">
                        <strong>Next Steps:</strong>
                    </p>
                    <ol class="mt-2 text-sm text-blue-700 list-decimal list-inside space-y-1">
                        <li>Ensure DNS is properly configured</li>
                        <li>Wait for DNS propagation (5-30 minutes)</li>
                        <li>Verify the domain via your tenant dashboard</li>
                    </ol>
                </div>
                
                <div class="mt-6 text-sm text-gray-500">
                    <p>Please contact your administrator if you need assistance.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
