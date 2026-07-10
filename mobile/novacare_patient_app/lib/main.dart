import 'package:flutter/material.dart';
import 'app.dart';
import 'core/network/api_client.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  
  final apiClient = ApiClient();
  
  runApp(NovaCareApp(apiClient: apiClient));
}
