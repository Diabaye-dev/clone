' Macro VBA pour créer automatiquement un fichier Excel
Sub CreerFichierExcel()
    Dim xlApp As Object
    Dim wb As Object
    Dim chemin As String

    chemin = "C:\\temp\\NouveauFichier.xlsx"

    Set xlApp = CreateObject("Excel.Application")
    Set wb = xlApp.Workbooks.Add

    wb.SaveAs Filename:=chemin
    wb.Close SaveChanges:=False
    xlApp.Quit

    Set wb = Nothing
    Set xlApp = Nothing
End Sub

