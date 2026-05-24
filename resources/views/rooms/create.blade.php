@extends('layouts.app')

@section('title', 'Create a Room — First Mediator')
@section('page-title', 'Create a Room — First Mediator')

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

        <form @submit.prevent="openSummaryModal">
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
                            <div class="p-4 rounded-lg border-2 transition-all"
                                 style="background-color: var(--bg-primary); border-color: var(--border-color);"
                                 :style="formData.category === cat.value ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                                <div class="flex items-start">
                                    <div class="p-2 rounded-lg mr-3 flex-shrink-0" style="background-color: #0D1B2A;">
                                        <svg class="w-6 h-6" style="color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
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

                    {{-- Custom Category Card --}}
                    <label class="relative cursor-pointer">
                        <input type="radio"
                               name="category"
                               value="custom"
                               x-model="formData.category"
                               class="sr-only peer">
                        <div class="p-4 rounded-lg border-2 transition-all h-full"
                             style="background-color: var(--bg-primary); border-color: var(--border-color);"
                             :style="formData.category === 'custom' ? 'border-color: var(--gold); background-color: rgba(201, 168, 76, 0.05);' : ''">
                            <div class="flex items-start">
                                <div class="p-2 rounded-lg mr-3 flex-shrink-0" style="background-color: #0D1B2A;">
                                    <svg class="w-6 h-6" style="color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-medium mb-1" style="color: var(--text-primary);">Other / Custom</h3>
                                    <p class="text-xs mb-2" style="color: var(--text-secondary);">Describe your own dispute type</p>
                                    <input x-show="formData.category === 'custom'"
                                           x-model="formData.custom_category"
                                           type="text"
                                           placeholder="e.g. Neighbour dispute"
                                           maxlength="60"
                                           class="w-full px-3 py-1.5 rounded border text-sm"
                                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                           @click.stop>
                                </div>
                            </div>
                        </div>
                    </label>
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
                            @foreach($jurisdictions as $region => $names)
                                <optgroup label="{{ $region }}">
                                    @foreach($names as $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Language: English only, hidden -->
                    <input type="hidden" x-model="formData.language" value="english">
                </div>
            </div>

            <!-- Step 3: Case Summary -->
            <div x-show="currentStep === 2" x-transition>
                <h2 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Case Summary</h2>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">Provide a brief summary and title to help First Mediator understand your dispute before the session</p>
                
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium" style="color: var(--text-primary);">Dispute Title</label>
                        <span class="text-xs" style="color: var(--text-secondary);" x-text="formData.title.length + '/80'"></span>
                    </div>
                    <input type="text" x-model="formData.title"
                           maxlength="80"
                           placeholder="e.g. Unpaid Invoice for Logo Design"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

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
                                            <p class="text-2xl font-bold mb-2" style="color: var(--gold);" x-text="'{{ $currencySymbol }}' + plan.price.toLocaleString()"></p>
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
                                    <p class="text-lg font-bold" style="color: var(--gold);" x-text="'{{ $currencySymbol }}' + getSelectedPlanPrice().toLocaleString()"></p>
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
                                    <p class="text-lg font-bold" style="color: var(--gold);" x-text="'{{ $currencySymbol }}' + getSelectedPlanSplitPrice().toLocaleString() + ' each'"></p>
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
                        :disabled="!canProceed()"
                        class="px-6 py-2 rounded-lg text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background-color: var(--gold);">
                    Review Summary & Pay
                </button>
            </div>
        </form>

        <!-- Pre-Payment Summary Modal -->
        <div x-show="showSummaryModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm" @click="showSummaryModal = false"></div>
            <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto p-8 rounded-2xl shadow-2xl border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-6 border-b pb-4" style="border-color: var(--border-color);">
                    <h3 class="text-2xl font-serif" style="color: var(--text-primary);">Mediation Session Summary</h3>
                    <button type="button" @click="showSummaryModal = false" class="p-2 rounded-lg hover:bg-opacity-10 hover:bg-gray-500" style="color: var(--text-secondary);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="space-y-4 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2 p-4 rounded-xl border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <p class="text-[10px] uppercase tracking-wider font-bold opacity-60 mb-1" style="color: var(--gold);">Dispute Title</p>
                            <p class="font-medium text-sm capitalize" style="color: var(--text-primary);" x-text="formData.title || 'Untitled'"></p>
                        </div>
                        <div class="p-4 rounded-xl border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <p class="text-[10px] uppercase tracking-wider font-bold opacity-60 mb-1" style="color: var(--gold);">Dispute Category</p>
                            <p class="font-medium text-sm capitalize" style="color: var(--text-primary);" x-text="formData.category"></p>
                        </div>
                        <div class="p-4 rounded-xl border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <p class="text-[10px] uppercase tracking-wider font-bold opacity-60 mb-1" style="color: var(--gold);">Jurisdiction</p>
                            <p class="font-medium text-sm" style="color: var(--text-primary);" x-text="formData.jurisdiction"></p>
                        </div>
                        <div class="md:col-span-2 p-4 rounded-xl border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <p class="text-[10px] uppercase tracking-wider font-bold opacity-60 mb-1" style="color: var(--gold);">Invited Party Email (Party B)</p>
                            <p class="font-medium text-sm" style="color: var(--text-primary);" x-text="formData.party_b_email"></p>
                        </div>
                    </div>
                    
                    <div class="p-4 rounded-xl border" style="background-color: var(--bg-primary); border-color: var(--border-color);" x-data="{ expanded: false }">
                        <p class="text-[10px] uppercase tracking-wider font-bold opacity-60 mb-2" style="color: var(--gold);">Case Summary Overview</p>
                        <p class="text-sm italic opacity-90 transition-all duration-300" 
                           :class="expanded ? '' : 'line-clamp-3'" 
                           style="color: var(--text-primary);" x-text="formData.case_summary"></p>
                        <button type="button" 
                                x-show="formData.case_summary.length > 150"
                                @click="expanded = !expanded" 
                                class="text-xs italic font-light mt-2 hover:underline focus:outline-none" style="color: var(--gold);">
                            <span x-text="expanded ? 'view less' : 'view more'"></span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl border" style="background-color: rgba(201, 168, 76, 0.15); border-color: var(--gold);">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-bold opacity-60 mb-1" style="color: var(--text-primary);">Total Amount Due</p>
                            <p class="text-sm font-medium" style="color: var(--text-primary);" x-text="formData.payment_type === 'split' ? 'Split Payment' : 'Full Payment'"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold" style="color: var(--gold);" x-text="'{{ $currencySymbol }}' + (formData.payment_type === 'split' ? getSelectedPlanSplitPrice() : getSelectedPlanPrice()).toLocaleString()"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t pt-6" style="border-color: var(--border-color);">
                    <label class="flex items-start gap-3 cursor-pointer mb-6">
                        <input type="checkbox" x-model="agreedToTerms" class="mt-1 w-4 h-4 rounded border-gray-300 text-gold focus:ring-gold" style="accent-color: var(--gold);">
                        <span class="text-sm" style="color: var(--text-secondary);">
                            I confirm that the case details provided are accurate to the best of my knowledge and I agree to First Mediator's <a href="{{ route('terms') }}" target="_blank" class="underline hover:text-gold">Terms of Service</a> and <a href="{{ route('privacy') }}" target="_blank" class="underline hover:text-gold">Privacy Policy</a>.
                        </span>
                    </label>
                    <button type="button"
                            @click="submitForm"
                            :disabled="!agreedToTerms || submitting"
                            class="w-full py-4 rounded-xl text-white font-bold uppercase tracking-widest shadow-lg transition-transform disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%);"
                            :class="agreedToTerms && !submitting ? 'hover:scale-[1.02] active:scale-95' : ''">
                        <span x-show="!submitting">Proceed to Payment</span>
                        <span x-show="submitting">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function roomCreation() {
    return {
        currentStep: 0,
        submitting: false,
        showSummaryModal: false,
        agreedToTerms: false,
        steps: ['Category', 'Jurisdiction', 'Summary', 'Payment'],
        formData: {
            category: '',
            custom_category: '',
            jurisdiction: '',
            language: 'english',
            title: '',
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
            { value: 'debt', label: 'Debt', description: 'Money owed & loan disputes', color: '#0F766E', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
            { value: 'marriage', label: 'Marriage', description: 'Marital & family disputes', color: '#DB2777', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' }
        ],
        languages: [
            { value: 'english', label: 'English' }
        ],
        plans: {!! json_encode($packages) !!},
        
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
                    return this.formData.category !== '';
                case 1:
                    return this.formData.jurisdiction !== '' && this.formData.language !== '';
                case 2:
                    return this.formData.title.trim().length > 0 && this.formData.case_summary.length >= 50 && this.formData.case_summary.length <= 2000;
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

        getSelectedPlanSplitPrice() {
            const plan = this.plans.find(p => p.duration === this.formData.duration);
            return plan ? plan.split : 0;
        },
        
        openSummaryModal() {
            if (!this.canProceed() || this.submitting) return;
            this.showSummaryModal = true;
        },
        
        async submitForm() {
            if (!this.canProceed() || !this.agreedToTerms || this.submitting) return;
            
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
