<?php
public function handle(Request $request)
{
    // juste renvoyer les données pour test
    return response()->json([
        'status' => 'ok',
        'data' => $request->all()
    ]);
}
