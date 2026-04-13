<div
    x-data="{}"
    x-init="
        if (window.companyLookupBindingsLoaded) {
            return
        }

        window.companyLookupBindingsLoaded = true

        const onlyDigits = (value) => (value || '').replace(/\D/g, '')

        const updateInput = (id, value) => {
            const input = document.getElementById(id)

            if (!input) {
                return
            }

            input.value = value ?? ''
            input.dispatchEvent(new Event('input', { bubbles: true }))
            input.dispatchEvent(new Event('change', { bubbles: true }))
        }

        const notifyError = (message) => {
            window.alert(message)
        }

        document.addEventListener('click', async (event) => {
            const cnpjButton = event.target.closest('#buscar-cnpj-empresa')

            if (cnpjButton) {
                const cnpj = onlyDigits(document.getElementById('empresa-cnpj')?.value)

                if (cnpj.length !== 14) {
                    notifyError('Informe um CNPJ válido para realizar a busca.')
                    return
                }

                cnpjButton.disabled = true

                try {
                    const response = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`)

                    if (!response.ok) {
                        throw new Error()
                    }

                    const data = await response.json()

                    updateInput('empresa-nome', data.nome_fantasia || data.razao_social || '')
                    updateInput('empresa-telefone', data.ddd_telefone_1 || '')
                    updateInput('empresa-cep', data.cep || '')
                    updateInput('empresa-endereco', data.logradouro || '')
                    updateInput('empresa-numero', data.numero || '')
                    updateInput('empresa-complemento', data.complemento || '')
                    updateInput('empresa-bairro', data.bairro || '')
                    updateInput('empresa-cidade', data.municipio || '')
                    updateInput('empresa-uf', data.uf || '')
                } catch (error) {
                    notifyError('Não foi possível consultar o CNPJ informado.')
                } finally {
                    cnpjButton.disabled = false
                }
            }

            const cepButton = event.target.closest('#buscar-cep-empresa')

            if (cepButton) {
                const cep = onlyDigits(document.getElementById('empresa-cep')?.value)

                if (cep.length !== 8) {
                    notifyError('Informe um CEP válido para realizar a busca.')
                    return
                }

                cepButton.disabled = true

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`)

                    if (!response.ok) {
                        throw new Error()
                    }

                    const data = await response.json()

                    if (data.erro) {
                        throw new Error()
                    }

                    updateInput('empresa-endereco', data.logradouro || '')
                    updateInput('empresa-complemento', data.complemento || '')
                    updateInput('empresa-bairro', data.bairro || '')
                    updateInput('empresa-cidade', data.localidade || '')
                    updateInput('empresa-uf', data.uf || '')
                } catch (error) {
                    notifyError('Não foi possível consultar o CEP informado.')
                } finally {
                    cepButton.disabled = false
                }
            }
        })
    "
></div>
<?php /**PATH C:\Users\conlkv\SISTEMA_LOCAÇÃO\backend\resources\views/filament/forms/company-search-scripts.blade.php ENDPATH**/ ?>