param($a)

$b = $a -replace "x64", "x86"

$depsA = Get-Content $a
$depsB = Get-Content $b

for ($i = 0; $i -lt $depsA.Length; $i++) {
    $depA = $depsA[$i].split("-")
    $depA = $depA[0..$($depA.Length - 3)]
    $depA = $depA -join "-"

    $depB = $depsB[$i].split("-")
    $depB = $depB[0..$($depB.Length - 3)]
    $depB = $depB -join "-"

    if ($depB -ne $depA) {
        Write-Output "$depA vs. $depB"
    }
}
if ($depsB.Length -ne $depsA.Length) {
    Write-Output "$a and $b have different count"
}
