<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proposal Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .header, .footer {
            text-align: center;
            margin: 20px 0;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        .content {
            margin: 20px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table, .table th, .table td {
            border: 1px solid #ccc;
        } 

        .table th {
            padding: 8px;
            text-align: left;
            width:30%;
        }

        .table th, .table td {
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .section-title {
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>UoK Final Research Proposal</h1>
    <p>Proposal Code: {{ $proposal->proposalcode }}</p>
    <p>Date Submitted: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}</p>
</div>

<div class="content">

    <div class="section-title">Applicant Details</div>
    <table class="table">
        <tr>
            <th>Name</th>
            <td>{{ $proposal->applicant->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $proposal->applicant->email }}</td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td>{{ $proposal->applicant->phonenumber }}</td>
        </tr>
        <tr>
            <th>Gender</th>
            <td>{{ $proposal->applicant->gender }}</td>
        </tr>
    </table>

    <div class="section-title">Proposal Details</div>
    <table class="table">
        <tr>
            <th>Research Title</th>
            <td>{{ $proposal->researchtitle }}</td>
        </tr>
        <tr>
            <th>Commencing Date</th>
            <td>{{ $proposal->commencingdate }}</td>
        </tr>
        <tr>
            <th>Termination Date</th>
            <td>{{ $proposal->terminationdate }}</td>
        </tr>
        <tr>
            <th>Department</th>
            <td>{{ $proposal->department->description }}</td>
        </tr>
        <tr>
            <th>Theme</th>
            <td>{{ $proposal->themeitem->themename }}</td>
        </tr>
    </table>

    <div class="section-title">Research Objectives</div>
    <p>{{ $proposal->objectives }}</p>

    <div class="section-title">Hypothesis</div>
    <p>{{ $proposal->hypothesis }}</p>

    <div class="section-title">Significance</div>
    <p>{{ $proposal->significance }}</p>

    <div class="section-title">Ethical Considerations</div>
    <p>{{ $proposal->ethicals }}</p>

    <div class="section-title">Expected Output</div>
    <p>{{ $proposal->expoutput }}</p>

    <div class="section-title">Social Impact</div>
    <p>{{ $proposal->socio_impact }}</p>

    <div class="section-title">Research Findings</div>
    <p>{{ $proposal->res_findings }}</p>

    <div class="section-title">Additional Comments</div>
    <p>{{ $proposal->comment }}</p>

</div>

<div class="footer">
    <p><b>Disclaimer: </b><i>This is a computer generated document and does not require signature.</i></p>
    <p>&copy; {{ \Carbon\Carbon::now()->format('Y') }} University of Kabianga</p>
</div>

</body>
</html>
