@extends('layouts.master')

@section('content')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let password = prompt("Enter password to terminate the system:");
 
    if (password !== null) {  // If the user didn't press "Cancel"
        fetch("{{ url('/terminate-action') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Show success message
                location.href = "{{ url('/dashboard/developer') }}"; // Redirect to home or another page
            } else {
                alert(data.message); // Show error message
                location.href = "{{ url('/dashboard/developer') }}"; // Redirect to home
            }
        })
        .catch(error => console.error("Error:", error));
    } else {
        location.href = "{{ url('/dashboard/developer') }}"; // Redirect if the user cancels
    }
});
</script>
@endsection
