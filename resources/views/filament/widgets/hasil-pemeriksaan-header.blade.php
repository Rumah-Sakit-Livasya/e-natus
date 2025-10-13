<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
    <label for="pemeriksaanTypeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        Pilih Jenis Pemeriksaan
    </label>
    <select id="pemeriksaanTypeFilter" wire:model.live="filterData.pemeriksaanType"
        class="block w-full max-w-xs mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
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
