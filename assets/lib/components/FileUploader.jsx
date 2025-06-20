import React, { useState } from 'react';
import {useFileValidator} from "../validators/FileValidator";

const FileUploader = () => {
    const [file, setFile] = useState(null);
    const [loading, setLoading] = useState(false);
    const [downloadUrl, setDownloadUrl] = useState(null);

    const { validate, error } = useFileValidator(['csv', 'xlsx']);

    const handleFileChange = (e) => {
        const selectedFile = e.target.files?.[0] || null;
        setDownloadUrl(null);

        if (!validate(selectedFile)) {
            setFile(null);
            return;
        }

        setFile(selectedFile);
    };

    const handleConvert = async () => {
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        setLoading(true);
        setDownloadUrl(null);

        try {
            const response = await fetch('/api/convert', {
                method: 'POST',
                body: formData,
            });

            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.error || 'De conversie is mislukt.');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            setDownloadUrl(url);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <input
                type="file"
                accept=".csv,.xlsx"
                onChange={handleFileChange}
                className="mb-4 block w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary"
            />

            <button
                onClick={handleConvert}
                disabled={!file || loading}
                className="w-full bg-primary text-white font-medium py-2 px-4 rounded-xl transition disabled:opacity-50 cursor-pointer hover:bg-white duration-200 ease-in-out hover:text-primary"
            >
                {loading ? 'Conversie is bezig...' : 'Converteren'}
            </button>

            {loading && (
                <p className="mt-4 text-sm text-gray-500">Even geduld terwijl uw bestand omgezet wordt...</p>
            )}

            {error && (
                <p className="mt-4 text-sm text-error">{error}</p>
            )}

            {downloadUrl && (
                <div className="mt-4 flex flex-col">
                    <p>Je bestand is omgezet geweest. Je kan het via de knop hieronder downloaden!</p>
                    <a
                        href={downloadUrl}
                        download={`converted.${file?.name.endsWith('.csv') ? 'xlsx' : 'csv'}`}
                        className="flex items-center justify-center gap-2 font-medium bg-transparent pb-2 border-bottom-2 border-success hover:opacity-50 transition-all ease-in-out duration-200"
                    >
                        Download geconverteerd bestand
                    </a>
                </div>
            )}
        </>
    );
};

export default FileUploader;
