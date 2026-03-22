@extends('layouts.app')

@section('title', 'Create a Room — FirstMediator')
@section('page-title', 'Create a Room')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-xl shadow-sm border p-6 lg:p-8"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);"
         x-data="roomCreation()"
         x-init="init()">
        
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <template x-for="(stepName, index) in steps" :key="index">
                    <div class="flex items-center" :class="index < steps.length - 1 ? 'flex-1' : ''">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-colors"
                                 :class="currentStep > index ? 'bg-gold text-white' : currentStep === index ? 'bg-gold text-white' : 'bg-gray-200 text-gray-600'">
                                <span x-text="index + 1"></span>
                            </div>
                            <span class="text-xs mt-2 text-center transition-colors" 
                                  :style="currentStep >= index ? 'color: var(--gold); font-weight: 600;' : 'color: var(--text-secondary);'"
                                  x-text="stepName"></span>
                        </div>
                        <div x-show="index < steps.length - 1" 
                             class="flex-1 h-0.5 mx-4"
                             :class="currentStep > index ? 'bg-gold' : 'bg-gray-200'"></div>
                    </div>
                </template>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <!-- Step 1: Dispute Category -->
            <div x-show="currentStep === 0" x-transition>
                <h2 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Select Dispute Category</h2>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">Choose the category that best describes your dispute, or create your own</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <template x-for="cat in categories" :key="cat.value">
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="category" 
                                   :value="cat.value"
                                   x-model="formData.category"
                                   @change="formData.custom_category = ''"
                                   class="sr-only peer">
                            <div class="p-4 rounded-lg border-2 transition-all peer-checked:border-gold peer-checked:bg-gold/5"
                                 style="background-color: var(--bg-primary); border-color: var(--border-color);"
                                 :style="formData.category === cat.value ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                                <div class="flex items-start">
                                    <div class="p-2 rounded-lg mr-3" :style="`background-color: ${cat.color}20`">
                                        <svg class="w-6 h-6" :style="`color: ${cat.color}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="cat.icon"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium mb-1" style="color: var(--text-primary);" x-text="cat.label"></h3>
                                        <p class="text-xs" style="color: var(--text-secondary);" x-text="cat.description"></p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>

                <!-- Custom Category Option -->
                <div class="p-4 rounded-lg border-2 transition-all"
                     style="background-color: var(--bg-primary); border-color: var(--border-color);"
                     :style="formData.category === 'custom' ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                    <label class="flex items-center cursor-pointer mb-3">
                        <input type="radio" 
                               name="category" 
                               value="custom"
                               x-model="formData.category"
                               class="sr-only peer">
                        <div class="p-2 rounded-lg mr-3" style="background-color: rgba(201, 168, 76, 0.1);">
                            <svg class="w-6 h-6" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium" style="color: var(--text-primary);">Custom Category</h3>
                            <p class="text-xs" style="color: var(--text-secondary);">Create your own dispute category</p>
                        </div>
                    </label>
                    <div x-show="formData.category === 'custom'" x-transition class="mt-3">
                        <input type="text" 
                               x-model="formData.custom_category"
                               placeholder="e.g., Intellectual Property, Insurance Claim, etc."
                               class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>
                </div>
            </div>

            <!-- Step 2: Jurisdiction & Language -->
            <div x-show="currentStep === 1" x-transition>
                <h2 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Jurisdiction & Language</h2>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">Select the legal jurisdiction and preferred session language</p>
                
                <div class="space-y-6">
                    <!-- Jurisdiction -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Jurisdiction</label>
                        <select x-model="formData.jurisdiction" 
                                class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                                style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                            <option value="">Select jurisdiction...</option>
                            <optgroup label="United Kingdom">
                                <option value="England & Wales">England & Wales</option>
                                <option value="Scotland">Scotland</option>
                                <option value="Northern Ireland">Northern Ireland</option>
                            </optgroup>
                            <optgroup label="Europe">
                                <option value="Ireland">Ireland</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Spain">Spain</option>
                                <option value="Italy">Italy</option>
                                <option value="Netherlands">Netherlands</option>
                            </optgroup>
                            <optgroup label="North America">
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                            </optgroup>
                            <optgroup label="Africa">
                                <option value="South Africa">South Africa</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Kenya">Kenya</option>
                            </optgroup>
                            <optgroup label="Asia Pacific">
                                <option value="Australia">Australia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Singapore">Singapore</option>
                                <option value="India">India</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Language -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Preferred Session Language</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <template x-for="lang in languages" :key="lang.value">
                                <label class="relative cursor-pointer">
                                    <input type="radio" 
                                           name="language" 
                                           :value="lang.value"
                                           x-model="formData.language"
                                           class="sr-only peer">
                                    <div class="p-3 rounded-lg border-2 text-center transition-all"
                                         style="background-color: var(--bg-primary); border-color: var(--border-color);"
                                         :style="formData.language === lang.value ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                                        <span class="font-medium" style="color: var(--text-primary);" x-text="lang.label"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Case Summary -->
            <div x-show="currentStep === 2" x-transition>
                <h2 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Case Summary</h2>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">Provide a brief summary to help Lex understand your dispute before the session</p>
                
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Your Case Summary</label>
                    <textarea x-model="formData.case_summary"
                              rows="8"
                              placeholder="Describe your dispute in detail. Include key facts, dates, and what you're seeking to resolve..."
                              class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold resize-none"
                              style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);"></textarea>
                    <p class="text-xs mt-2" style="color: var(--text-secondary);">
                        <span x-text="formData.case_summary.length"></span> / 2000 characters
                    </p>
                </div>
            </div>

            <!-- Step 4: Duration & Payment -->
            <div x-show="currentStep === 3" x-transition>
                <h2 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Session Duration & Payment</h2>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">Choose your session duration and payment option</p>
                
                <div class="space-y-6">
                    <!-- Duration Plans -->
                    <div>
                        <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">Session Duration</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <template x-for="plan in plans" :key="plan.duration">
                                <label class="relative cursor-pointer">
                                    <input type="radio" 
                                           name="duration" 
                                           :value="plan.duration"
                                           x-model="formData.duration"
                                           class="sr-only peer">
                                    <div class="p-5 rounded-lg border-2 transition-all"
                                         style="background-color: var(--bg-primary); border-color: var(--border-color);"
                                         :style="formData.duration === plan.duration ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                                        <div class="text-center">
                                            <h3 class="font-bold text-lg mb-1" style="color: var(--text-primary);" x-text="plan.name"></h3>
                                            <p class="text-2xl font-bold mb-2" style="color: var(--gold);" x-text="'₦' + plan.price.toLocaleString()"></p>
                                            <p class="text-sm" style="color: var(--text-secondary);" x-text="plan.duration + ' minutes'"></p>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Payment Type -->
                    <div>
                        <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">Payment Option</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" 
                                       name="payment_type" 
                                       value="full"
                                       x-model="formData.payment_type"
                                       class="sr-only peer">
                                <div class="p-4 rounded-lg border-2 transition-all"
                                     style="background-color: var(--bg-primary); border-color: var(--border-color);"
                                     :style="formData.payment_type === 'full' ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                                    <h3 class="font-medium mb-1" style="color: var(--text-primary);">Pay Full Amount</h3>
                                    <p class="text-sm mb-2" style="color: var(--text-secondary);">You cover the entire session cost</p>
                                    <p class="text-lg font-bold" style="color: var(--gold);" x-text="'₦' + getSelectedPlanPrice().toLocaleString()"></p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" 
                                       name="payment_type" 
                                       value="split"
                                       x-model="formData.payment_type"
                                       class="sr-only peer">
                                <div class="p-4 rounded-lg border-2 transition-all"
                                     style="background-color: var(--bg-primary); border-color: var(--border-color);"
                                     :style="formData.payment_type === 'split' ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                                    <h3 class="font-medium mb-1" style="color: var(--text-primary);">Split Payment</h3>
                                    <p class="text-sm mb-2" style="color: var(--text-secondary);">Both parties pay half each</p>
                                    <p class="text-lg font-bold" style="color: var(--gold);" x-text="'₦' + (getSelectedPlanPrice() / 2).toLocaleString() + ' each'"></p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Party B Email -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Party B Email Address</label>
                        <input type="email" 
                               x-model="formData.party_b_email"
                               placeholder="other.party@example.com"
                               class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">We'll send them an invitation link to join the session</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8 pt-6 border-t" style="border-color: var(--border-color);">
                <button type="button"
                        @click="previousStep"
                        x-show="currentStep > 0"
                        class="px-6 py-2 rounded-lg border transition-colors"
                        style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--bg-primary);"
                        onmouseover="this.style.backgroundColor='var(--bg-secondary)'"
                        onmouseout="this.style.backgroundColor='var(--bg-primary)'">
                    Previous
                </button>
                <div x-show="currentStep === 0"></div>
                
                <button type="button"
                        @click="nextStep"
                        x-show="currentStep < 3"
                        :disabled="!canProceed()"
                        class="px-6 py-2 rounded-lg text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background-color: var(--gold);">
                    Next
                </button>
                
                <button type="submit"
                        x-show="currentStep === 3"
                        :disabled="!canProceed() || submitting"
                        class="px-6 py-2 rounded-lg text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background-color: var(--gold);">
                    <span x-show="!submitting">Create Room & Proceed to Payment</span>
                    <span x-show="submitting">Creating...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function roomCreation() {
    return {
        currentStep: 0,
        submitting: false,
        steps: ['Category', 'Jurisdiction', 'Summary', 'Payment'],
        formData: {
            category: '',
            custom_category: '',
            jurisdiction: '',
            language: 'english',
            case_summary: '',
            duration: '60',
            payment_type: 'full',
            party_b_email: ''
        },
        categories: [
            { value: 'tenancy', label: 'Tenancy', description: 'Landlord-tenant disputes', color: '#1D4ED8', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
            { value: 'freelance', label: 'Freelance', description: 'Contract & payment issues', color: '#15803D', icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
            { value: 'business', label: 'Business', description: 'Partnership & commercial disputes', color: '#C2410C', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
            { value: 'ecommerce', label: 'E-commerce', description: 'Online sales & delivery issues', color: '#7E22CE', icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' },
            { value: 'employment', label: 'Employment', description: 'Workplace & termination disputes', color: '#BE123C', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
            { value: 'debt', label: 'Debt', description: 'Money owed & loan disputes', color: '#0F766E', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }
        ],
        nigerianStates: [],
        languages: [
            { value: 'english', label: 'English' },
            { value: 'french', label: 'French' },
            { value: 'spanish', label: 'Spanish' },
            { value: 'german', label: 'German' },
            { value: 'italian', label: 'Italian' },
            { value: 'portuguese', label: 'Portuguese' },
            { value: 'arabic', label: 'Arabic' },
            { value: 'mandarin', label: 'Mandarin' }
        ],
        plans: [
            { name: 'Starter', duration: '30', price: 4500 },
            { name: 'Standard', duration: '60', price: 7500 },
            { name: 'Extended', duration: '90', price: 10000 }
        ],
        
        init() {
            // Initialize
        },
        
        nextStep() {
            if (this.canProceed()) {
                this.currentStep++;
            }
        },
        
        previousStep() {
            this.currentStep--;
        },
        
        canProceed() {
            switch(this.currentStep) {
                case 0:
                    if (this.formData.category === 'custom') {
                        return this.formData.custom_category.trim().length >= 3;
                    }
                    return this.formData.category !== '';
                case 1:
                    return this.formData.jurisdiction !== '' && this.formData.language !== '';
                case 2:
                    return this.formData.case_summary.length >= 50 && this.formData.case_summary.length <= 2000;
                case 3:
                    return this.formData.duration !== '' && 
                           this.formData.payment_type !== '' && 
                           this.formData.party_b_email !== '' &&
                           this.isValidEmail(this.formData.party_b_email);
                default:
                    return false;
            }
        },
        
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        
        getSelectedPlanPrice() {
            const plan = this.plans.find(p => p.duration === this.formData.duration);
            return plan ? plan.price : 0;
        },
        
        async submitForm() {
            if (!this.canProceed() || this.submitting) return;
            
            this.submitting = true;
            
            try {
                const response = await fetch('/rooms', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showToast('Room created successfully! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = data.payment_url;
                    }, 1000);
                } else {
                    showToast(data.message || 'An error occurred. Please try again.', 'error');
                    this.submitting = false;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
                this.submitting = false;
            }
        }
    }
}
</script>

<style>
.bg-gold {
    background-color: var(--gold);
}
.text-gold {
    color: var(--gold);
}
.border-gold {
    border-color: var(--gold);
}
</style>
@endsection
