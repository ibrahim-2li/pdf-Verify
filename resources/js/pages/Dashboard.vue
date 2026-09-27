<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PendingInvitationsModal from '@/components/PendingInvitationsModal.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { dashboard } from '@/routes';
import pdf from '@/routes/pdf';
import type { DashboardInvitation, Team } from '@/types';
import { ref } from 'vue';

const page = usePage();

const form = useForm({
    pdf_file: null as File | null,
});

const submit = () => {
    form.post(pdf.check.url(), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

defineProps<{
    pendingInvitations?: DashboardInvitation[];
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam
                    ? dashboard(props.currentTeam.slug)
                    : '/',
            },
        ],
    }),
});
</script>

<template>
    <Head title="Dashboard" />

    <PendingInvitationsModal
        v-if="pendingInvitations && pendingInvitations.length > 0"
        :invitations="pendingInvitations"
    />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <!-- <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div> -->
        <div
            class="relative flex flex-col items-center justify-center min-h-[50vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-6"
        >
            <h2 class="text-2xl font-bold mb-4">Check PDF Integrity</h2>
            <p class="text-sm text-gray-500 mb-6 max-w-md text-center">
                Upload a digitally signed PDF to verify if it has been modified since it was signed. 
                This requires the <code class="bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded">setasign/setapdf-signer</code> package.
            </p>

            <div v-if="$page.props.pdf_check_result" 
                 :class="[
                     'p-4 mb-6 rounded-md w-full max-w-md border overflow-hidden',
                     $page.props.pdf_check_result.type === 'error' ? 'bg-red-50 border-red-200' : 
                     ($page.props.pdf_check_result.type === 'success' ? 'bg-green-50 border-green-200' : 'bg-blue-50 border-blue-200')
                 ]">
                
                <h3 :class="[
                    'font-bold text-lg mb-2',
                    $page.props.pdf_check_result.type === 'error' ? 'text-red-700' : 
                    ($page.props.pdf_check_result.type === 'success' ? 'text-green-700' : 'text-blue-700')
                ]">
                    {{ $page.props.pdf_check_result.message }}
                </h3>

                <div v-if="$page.props.pdf_check_result.metadata" class="mt-4 border-t pt-4 border-gray-200">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">File Metadata</h4>
                    <dl class="space-y-2 text-sm text-gray-700">
                        <div v-if="$page.props.pdf_check_result.metadata.CreationDate" class="grid grid-cols-3 gap-2">
                            <dt class="font-medium">Creation Date:</dt>
                            <dd class="col-span-2 break-words">{{ $page.props.pdf_check_result.metadata.CreationDate }}</dd>
                        </div>
                        <div v-if="$page.props.pdf_check_result.metadata.ModDate" class="grid grid-cols-3 gap-2">
                            <dt class="font-medium">Modification Date:</dt>
                            <dd class="col-span-2 break-words">{{ $page.props.pdf_check_result.metadata.ModDate }}</dd>
                        </div>
                        <div v-if="$page.props.pdf_check_result.metadata.Producer" class="grid grid-cols-3 gap-2">
                            <dt class="font-medium">Producer:</dt>
                            <dd class="col-span-2 break-words">{{ $page.props.pdf_check_result.metadata.Producer }}</dd>
                        </div>
                        <div v-if="$page.props.pdf_check_result.metadata.Author" class="grid grid-cols-3 gap-2">
                            <dt class="font-medium">Author:</dt>
                            <dd class="col-span-2 break-words">{{ $page.props.pdf_check_result.metadata.Author }}</dd>
                        </div>
                        <div v-if="$page.props.pdf_check_result.metadata.Title" class="grid grid-cols-3 gap-2">
                            <dt class="font-medium">Title:</dt>
                            <dd class="col-span-2 break-words">{{ $page.props.pdf_check_result.metadata.Title }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <form @submit.prevent="submit" class="w-full max-w-md space-y-4">
                <div>
                    <label for="pdf_file" class="block text-sm font-medium mb-1">Select PDF File</label>
                    <input 
                        type="file" 
                        id="pdf_file" 
                        accept="application/pdf"
                        @input="form.pdf_file = $event.target.files[0]"
                        @change="form.pdf_file = $event.target.files[0]"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/90"
                    />
                    <div class="mt-2 text-xs text-green-600" v-if="form.pdf_file">
                        Selected: {{ form.pdf_file.name }}
                    </div>
                    <p v-if="form.errors.pdf_file" class="text-red-500 text-xs mt-1">{{ form.errors.pdf_file }}</p>
                </div>

                <button 
                    type="submit" 
                    :disabled="form.processing || form.pdf_file === null"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50"
                >
                    {{ form.processing ? 'Checking...' : 'Check PDF' }}
                </button>
            </form>
        </div>
    </div>
</template>
