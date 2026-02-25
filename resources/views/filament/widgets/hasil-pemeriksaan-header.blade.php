<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
    <label for="pemeriksaanTypeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        Pilih Jenis Pemeriksaan
    </label>
    <div class="mt-1">
        <select id="pemeriksaanTypeFilter" wire:model.live="filterData.pemeriksaanType"
            class="fi-input block w-full max-w-xs border-none bg-white py-1.5 text-base text-gray-950 outline-none ring-1 transition duration-75 placeholder:text-gray-400 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-50 sm:text-sm sm:leading-6 dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 fi-fo-select-input rounded-lg shadow-sm ring-gray-950/10 focus-visible:ring-primary-600 dark:ring-white/20 dark:focus-visible:ring-primary-500">
            <option value="ekg">EKG</option>
            <option value="lab">Pemeriksaan Lab</option>
            <option value="rontgen">Rontgen</option>
            <option value="audiometry">Audiometri</option>
            <option value="drug_test">Tes Narkoba</option>
            <option value="spirometry">Spirometri</option>
            <option value="treadmill">Treadmill</option>
            <option value="usg_abdomen">USG Abdomen</option>
            <option value="usg_mammae">USG Mammae</option>
        </select>
    </div>
</div>
