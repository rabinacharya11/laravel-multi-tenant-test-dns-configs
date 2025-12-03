<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tenant Dashboard - {{ $tenantName ?? 'Admin' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div x-data="dashboardApp()" x-init="init()" class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tenant Dashboard</h1>
                        <p class="text-sm text-gray-600 mt-1">Tenant ID: <span class="font-mono" x-text="tenantId"></span></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">Current Domain</p>
                            <p class="text-sm text-gray-600" x-text="currentDomain"></p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Overview
                    </button>
                    <button @click="activeTab = 'users'" 
                            :class="activeTab === 'users' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Users
                    </button>
                    <button @click="activeTab = 'domains'" 
                            :class="activeTab === 'domains' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Domain Management
                    </button>
                    <button @click="activeTab = 'instructions'" 
                            :class="activeTab === 'instructions' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        DNS Instructions
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" x-cloak>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <!-- Stats Cards -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                        <dd class="text-3xl font-semibold text-gray-900" x-text="stats.usersCount"></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Domains</dt>
                                        <dd class="text-3xl font-semibold text-gray-900" x-text="domains.length"></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Verified Domains</dt>
                                        <dd class="text-3xl font-semibold text-gray-900" x-text="domains.filter(d => d.is_verified).length"></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Verification</dt>
                                        <dd class="text-3xl font-semibold text-gray-900" x-text="domains.filter(d => !d.is_verified).length"></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <button @click="activeTab = 'domains'; showAddDomainModal = true" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Custom Domain
                        </button>
                        <button @click="activeTab = 'users'" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            View Users
                        </button>
                        <button @click="activeTab = 'instructions'" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            DNS Setup Guide
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Tab -->
            <div x-show="activeTab === 'users'" x-cloak>
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Users</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="user in users" :key="user.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.id"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="user.name"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.email"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="users.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No users found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Domains Tab -->
            <div x-show="activeTab === 'domains'" x-cloak>
                <div class="mb-4 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Your Domains</h3>
                    <button @click="showAddDomainModal = true" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Custom Domain
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="domain in domains" :key="domain.id">
                        <div class="bg-white shadow rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h4 class="text-lg font-medium text-gray-900" x-text="domain.domain"></h4>
                                        <span x-show="domain.is_primary" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Primary
                                        </span>
                                        <span x-show="domain.type === 'subdomain'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Subdomain
                                        </span>
                                        <span x-show="domain.type === 'custom'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Custom
                                        </span>
                                        <span x-show="domain.is_verified" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Verified
                                        </span>
                                        <span x-show="!domain.is_verified" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⏳ Pending
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Added on <span x-text="formatDate(domain.created_at)"></span>
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <button x-show="!domain.is_verified && domain.type === 'custom'" 
                                            @click="verifyDomain(domain.id)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        Verify DNS
                                    </button>
                                    <button x-show="domain.type === 'custom'" 
                                            @click="viewInstructions(domain.id)"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        View Instructions
                                    </button>
                                    <button x-show="!domain.is_primary && domain.is_verified" 
                                            @click="setPrimary(domain.id)"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Set as Primary
                                    </button>
                                    <button x-show="domain.type === 'custom' && !domain.is_primary" 
                                            @click="deleteDomain(domain.id)"
                                            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- DNS Instructions Tab -->
            <div x-show="activeTab === 'instructions'" x-cloak>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">DNS Setup Instructions</h3>
                    
                    <div class="prose max-w-none">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Your subdomain is ready!</strong> It works immediately at <span class="font-mono" x-text="currentDomain"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Option 1: Using Your Subdomain (Default)</h4>
                        <p class="text-gray-600 mb-4">Your tenant automatically gets a subdomain. HTTPS is already active via wildcard certificate.</p>
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <p class="font-mono text-sm" x-text="'https://' + currentDomain"></p>
                        </div>

                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Option 2: Using Your Own Custom Domain</h4>
                        <p class="text-gray-600 mb-4">If you want your site to appear as <code>yourdomain.com</code> instead of <span class="font-mono" x-text="currentDomain"></span>, follow these steps:</p>

                        <div class="space-y-6">
                            <div class="border-l-4 border-indigo-500 pl-4">
                                <h5 class="font-semibold text-gray-900 mb-2">Step 1: Configure DNS</h5>
                                <p class="text-gray-600 mb-3">Go to your domain provider (GoDaddy, Namecheap, Cloudflare, etc.) and create a CNAME record:</p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 border">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Value/Target</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">TTL</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td class="px-4 py-2 font-mono text-sm">CNAME</td>
                                                <td class="px-4 py-2 font-mono text-sm">@</td>
                                                <td class="px-4 py-2 font-mono text-sm">petmelo.com</td>
                                                <td class="px-4 py-2 font-mono text-sm">Auto</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">For www subdomain, use "www" as Name instead of "@"</p>
                            </div>

                            <div class="border-l-4 border-indigo-500 pl-4">
                                <h5 class="font-semibold text-gray-900 mb-2">Step 2: Wait for DNS Propagation</h5>
                                <p class="text-gray-600">Usually 5–30 minutes, sometimes up to 24 hours. Verify with:</p>
                                <div class="bg-gray-900 text-gray-100 p-3 rounded-md mt-2 font-mono text-sm">
                                    ping yourdomain.com
                                </div>
                            </div>

                            <div class="border-l-4 border-indigo-500 pl-4">
                                <h5 class="font-semibold text-gray-900 mb-2">Step 3: Add Domain to Dashboard</h5>
                                <p class="text-gray-600">Go to the "Domain Management" tab and click "Add Custom Domain". Enter your domain and click Save.</p>
                            </div>

                            <div class="border-l-4 border-indigo-500 pl-4">
                                <h5 class="font-semibold text-gray-900 mb-2">Step 4: Verify & Activate SSL</h5>
                                <p class="text-gray-600">Once DNS is configured, click "Verify DNS" button. The system will automatically issue SSL certificate and activate HTTPS.</p>
                            </div>
                        </div>

                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mt-6">
                            <p class="text-sm text-green-700">
                                ✅ SSL is automatic — no manual certificate installation required!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Add Domain Modal -->
        <div x-show="showAddDomainModal" 
             x-cloak
             class="fixed z-10 inset-0 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="showAddDomainModal = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add Custom Domain</h3>
                        
                        <div x-show="addDomainError" class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                            <p class="text-sm text-red-700" x-text="addDomainError"></p>
                        </div>

                        <div class="mb-4">
                            <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">Domain Name</label>
                            <input type="text" 
                                   x-model="newDomain"
                                   @input="newDomain = newDomain.replace(/^https?:\/\//, '').replace(/\/.*$/, '').toLowerCase()"
                                   placeholder="example.com or www.example.com"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                            <p class="mt-2 text-sm text-gray-500">Enter just the domain name (e.g., example.com). We'll automatically clean it!</p>
                            <p x-show="newDomain && newDomain.length > 0" class="mt-1 text-xs text-indigo-600">
                                Will be added as: <span class="font-mono" x-text="newDomain"></span>
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newDomainIsPrimary" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Set as primary domain</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="addDomain()"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Add Domain
                        </button>
                        <button type="button" 
                                @click="showAddDomainModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function dashboardApp() {
            return {
                activeTab: 'overview',
                tenantId: '',
                currentDomain: window.location.hostname,
                users: [],
                domains: [],
                stats: {
                    usersCount: 0
                },
                showAddDomainModal: false,
                newDomain: '',
                newDomainIsPrimary: false,
                addDomainError: '',

                async init() {
                    await this.loadStats();
                    await this.loadUsers();
                    await this.loadDomains();
                },

                async loadStats() {
                    try {
                        const response = await fetch('/stats');
                        const data = await response.json();
                        this.tenantId = data.tenant_id;
                        this.stats.usersCount = data.users_count;
                    } catch (error) {
                        console.error('Error loading stats:', error);
                    }
                },

                async loadUsers() {
                    try {
                        const response = await fetch('/users');
                        const data = await response.json();
                        this.users = data.users || [];
                    } catch (error) {
                        console.error('Error loading users:', error);
                    }
                },

                async loadDomains() {
                    try {
                        const response = await fetch('/api/domains');
                        const data = await response.json();
                        this.domains = data.domains || [];
                    } catch (error) {
                        console.error('Error loading domains:', error);
                    }
                },

                async addDomain() {
                    this.addDomainError = '';
                    
                    if (!this.newDomain) {
                        this.addDomainError = 'Please enter a domain name';
                        return;
                    }

                    // Clean the domain - remove http://, https://, www., trailing slashes, etc.
                    let cleanDomain = this.newDomain.trim()
                        .replace(/^https?:\/\//, '')  // Remove http:// or https://
                        .replace(/\/.*$/, '')          // Remove everything after first /
                        .toLowerCase();                // Convert to lowercase

                    console.log('Adding domain:', cleanDomain);

                    try {
                        const response = await fetch('/api/domains', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                domain: cleanDomain,
                                is_primary: this.newDomainIsPrimary
                            })
                        });

                        const data = await response.json();
                        console.log('Response:', data);

                        if (!response.ok) {
                            this.addDomainError = data.errors ? data.errors.join(', ') : data.error || 'Failed to add domain';
                            console.error('Error adding domain:', this.addDomainError);
                            return;
                        }

                        this.showAddDomainModal = false;
                        this.newDomain = '';
                        this.newDomainIsPrimary = false;
                        await this.loadDomains();
                        alert('✅ Domain added successfully! Please configure DNS and verify.');
                    } catch (error) {
                        console.error('Exception:', error);
                        this.addDomainError = 'Error adding domain: ' + error.message;
                    }
                },

                async verifyDomain(domainId) {
                    try {
                        const response = await fetch(`/api/domains/${domainId}/verify`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ ' + data.message);
                            await this.loadDomains();
                        } else {
                            alert('❌ ' + data.message);
                        }
                    } catch (error) {
                        alert('Error verifying domain: ' + error.message);
                    }
                },

                async setPrimary(domainId) {
                    try {
                        const response = await fetch(`/api/domains/${domainId}/set-primary`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ Domain set as primary');
                            await this.loadDomains();
                        } else {
                            alert('❌ ' + data.error);
                        }
                    } catch (error) {
                        alert('Error setting primary domain: ' + error.message);
                    }
                },

                async deleteDomain(domainId) {
                    if (!confirm('Are you sure you want to delete this domain?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/api/domains/${domainId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ Domain deleted successfully');
                            await this.loadDomains();
                        } else {
                            alert('❌ ' + data.error);
                        }
                    } catch (error) {
                        alert('Error deleting domain: ' + error.message);
                    }
                },

                viewInstructions(domainId) {
                    this.activeTab = 'instructions';
                },

                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
