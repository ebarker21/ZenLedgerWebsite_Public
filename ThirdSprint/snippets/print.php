<div style="text-align: center; margin-top: 2rem;">
    <button onclick="window.print()">🖨️ Print</button>
    <button onclick="downloadPDF()">⬇️ Download PDF</button>
    <button onclick="window.location.href='mail-page.php'">✉️ Email</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    async function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text(document.title, 90, 20);
        doc.autoTable({
            html: 'table',
            startY: 30,
            theme: 'grid'
        });
        doc.save(document.title.toLowerCase().replace(/\s/g, '-') + ".pdf");
    }
</script>