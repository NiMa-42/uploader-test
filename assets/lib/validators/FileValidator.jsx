import { useCallback, useState } from 'react';

export const useFileValidator = (allowedExtensions = ['csv', 'xlsx']) => {
    const [error, setError] = useState(null);

    const validate = useCallback((file) => {
        if (!file) {
            setError('Geen bestand geselecteerd.');
            return false;
        }

        const extension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(extension)) {
            setError(`Ongeldig bestandstype: .${extension}. Enkel ${allowedExtensions.join(', ')} toegestaan.`);
            return false;
        }

        setError(null);
        return true;
    }, [allowedExtensions]);

    return { validate, error };
};
