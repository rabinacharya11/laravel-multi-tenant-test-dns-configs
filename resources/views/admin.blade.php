<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Admin - Multi-Tenant Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div x-data="adminApp()" x-init="init()" class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Central Admin Dashboard</h1>
                        <p class="text-sm text-gray-600 mt-1">Multi-Tenant Platform Management</p>
                    </div>
                    <button @click="showCreateTenantModal = true" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create New Tenant
                    </button>
                </div>
            </div>
        </header>

        <!-- Stats -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Tenants</dt>
                                    <dd class="text-3xl font-semibold text-gray-900" x-text="tenants.length"></dd>
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
                                    <dd class="text-3xl font-semibold text-gray-900" x-text="totalDomains"></dd>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Today</dt>
                                    <dd class="text-3xl font-semibold text-gray-900" x-text="tenants.length"></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tenants List -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">All Tenants</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primary Domain</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domains</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="tenant in tenants" :key="tenant.id">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900" x-text="tenant.name || 'Unnamed Tenant'"></div>
                                            <div class="text-sm text-gray-500 font-mono" x-text="tenant.id"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a :href="'https://' + tenant.primary_domain" 
                                               target="_blank"
                                               class="text-sm text-indigo-600 hover:text-indigo-900 font-mono"
                                               x-text="tenant.primary_domain || 'N/A'"></a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                  x-text="tenant.domains_count + ' domain' + (tenant.domains_count !== 1 ? 's' : '')">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(tenant.created_at)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a :href="'https://' + tenant.primary_domain + '/dashboard'" 
                                               target="_blank"
                                               class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                View
                                            </a>
                                            <button @click="viewTenantDetails(tenant.id)" 
                                                    class="text-gray-600 hover:text-gray-900">
                                                Details
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="tenants.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No tenants found. Create your first tenant to get started.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Tenant Modal -->
        <div x-show="showCreateTenantModal" 
             x-cloak
             class="fixed z-10 inset-0 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="showCreateTenantModal = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Tenant</h3>
                        
                        <div x-show="createTenantError" class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                            <p class="text-sm text-red-700" x-text="createTenantError"></p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="tenant-name" class="block text-sm font-medium text-gray-700 mb-2">Tenant Name</label>
                                <input type="text" 
                                       x-model="newTenant.name"
                                       placeholder="My Business"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                            </div>

                            <div>
                                <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-2">Subdomain</label>
                                <div class="flex rounded-md shadow-sm">
                                    <input type="text" 
                                           x-model="newTenant.subdomain"
                                           placeholder="mybusiness"
                                           class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-l-md px-3 py-2 border">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                        .petmelo.com
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Only lowercase letters, numbers, and hyphens allowed</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="createTenant()"
                                :disabled="!newTenant.name || !newTenant.subdomain"
                                :class="(!newTenant.name || !newTenant.subdomain) ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Create Tenant
                        </button>
                        <button type="button" 
                                @click="showCreateTenantModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adminApp() {
            return {
                tenants: [],
                totalDomains: 0,
                showCreateTenantModal: false,
                newTenant: {
                    name: '',
                    subdomain: ''
                },
                createTenantError: '',

                async init() {
                    await this.loadTenants();
                },

                async loadTenants() {
                    try {
                        const response = await fetch('/api/tenants');
                        const data = await response.json();
                        this.tenants = data.tenants || [];
                        this.totalDomains = this.tenants.reduce((sum, t) => sum + (t.domains_count || 0), 0);
                    } catch (error) {
                        console.error('Error loading tenants:', error);
                    }
                },

                async createTenant() {
                    this.createTenantError = '';
                    
                    if (!this.newTenant.name || !this.newTenant.subdomain) {
                        this.createTenantError = 'Please fill in all fields';
                        return;
                    }

                    // Validate subdomain format
                    const subdomainRegex = /^[a-z0-9][a-z0-9-]*[a-z0-9]$/;
                    if (!subdomainRegex.test(this.newTenant.subdomain)) {
                        this.createTenantError = 'Invalid subdomain format. Use only lowercase letters, numbers, and hyphens.';
                        return;
                    }

                    try {
                        const response = await fetch('/api/tenants', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.newTenant)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.createTenantError = data.error || 'Failed to create tenant';
                            return;
                        }

                        this.showCreateTenantModal = false;
                        this.newTenant = { name: '', subdomain: '' };
                        await this.loadTenants();
                        
                        alert(`✅ Tenant created successfully!\n\nAccess at: ${data.tenant.url}`);
                    } catch (error) {
                        this.createTenantError = 'Error creating tenant: ' + error.message;
                    }
                },

                viewTenantDetails(tenantId) {
                    // Future: Could open a detailed modal with more tenant information
                    alert('Tenant ID: ' + tenantId);
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
