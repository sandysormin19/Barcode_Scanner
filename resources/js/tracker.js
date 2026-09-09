document.addEventListener("DOMContentLoaded", function () {
    const statusForm = document.getElementById("status-form");

    if (!statusForm) {
        return;
    }

    statusForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const formData = new FormData(statusForm);
        const payload = Object.fromEntries(formData.entries());

        const submitButton = statusForm.querySelector('button[type="submit"]');
        const originalText = submitButton?.textContent ?? "Update Status";

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = "Memperbarui...";
        }

        try {
            const response = await fetch(statusForm.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || "Gagal memperbarui status.");
            }

            window.location.reload();
        } catch (error) {
            alert(error.message || "Terjadi kesalahan.");
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        }
    });
});
