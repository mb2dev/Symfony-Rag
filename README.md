# Symfony Rag

This project is a Symfony-based application implementing Retrieval-Augmented Generation (RAG) using Ollama for LLM inference and Elasticsearch as the vector store and search engine.


## 🚀 Features

- **AI-Powered Search:** Uses Ollama to generate responses based on retrieved documents and allows selecting multiple different models.
- **Elasticsearch Integration:** Stores and retrieves relevant documents efficiently.
- **User Experience (UX):** Simple interface to ask questions to the RAG system

---

## 🛠️ Requirements

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Make](https://www.gnu.org/software/make/)

---

## 📦 Installation

1. Clone this repository
    ```bash
      git clone <repository-url>
      cd <repository-directory>
    ```

2. Initialize the Symfony project

    ```bash
      make init
    ```
    The application will be available at: http://127.0.0.1:8080/. <br>
    Your Symfony application will be available under the app folder.


3. Open Ollama UI to download a model
   - Open Ollama UI [Open UI](https://127.0.0.1:3000/) in your browser.
   - Create an account, then navigate to Settings → Admin Settings → Model, and select Manage Models.

    ![Open UI ](/assets/images/openui-model.png)

    - Download and select a model to use from ollama.com.

    ![Open UI ](/assets/images/openui-model-download.png)

   - In the Symfony project, configure the model by updating the model variable with your right model .env.dev
    ```bash
     OLLAMA_MODEL=deepseek-r1:latest
    ```

4. Generate embeddings

   - Connect to the PHP container
    ```bash
      make exec-php-fpm
     ```
   - Run the Symfony command to compute and save embeddings to Elasticsearch
   ```bash
    bin/console GenerateEmbeddings
    ```

5. Ask your questions :

    Move to the  http://127.0.0.1:8080/ url to ask you question.

    ![Ask a question ](/assets/images/ui-ask-question.png)

### Example Questions:
  - Which past interventions were similar to the recent repair of **"Industrial Oven X200"**?
  - Who has the most experience working on industrial ovens?
  - Which repairs were delayed due to missing parts?
  - Can you provide a summary of all interventions related to the **Hydraulic Press P789**, including the type of intervention, date, technician, and parts replaced? Additionally, please write a summary email for a customer to provide visibility on this equipment's maintenance and status.
  - And more...

Feel free to ask any questions related to your equipment maintenance or interventions!

## 🔧 Available Commands

You can find the list of default make available Cmd under the repository https://github.com/mb2dev/Symfony-Docker-Boilerplate

# 🐳 Docker Services

| Service      | Image & Version                                | Port(s)          | Description                                       |
|--------------|-----------------------------------------------|------------------|---------------------------------------------------|
| **PHP-FPM**  | `./docker/php-fpm`                            | N/A              | Symfony backend service.                          |
| **Nginx**    | `nginx:alpine`                                | 8080             | Serves the application at http://127.0.0.1:8080. |
| **Elasticsearch** | `docker.elastic.co/elasticsearch/elasticsearch:8.16.4` | 9200 | Used for storing and searching documents.  http://127.0.0.1:9200/        |
| **Kibana**   | `docker.elastic.co/kibana/kibana:8.5.0`      | 5601             | UI for interacting with Elasticsearch.  http://127.0.0.1:5601/          |
| **Ollama**   | `ollama/ollama:0.5.13-rocm`                   | 11434            | Interface for querying and interacting with LLM.  |
| **Open WebUI** | `ghcr.io/open-webui/open-webui:main`         | 3000             | Interface for managing models in Ollama.   http://127.0.0.1:300/       |


# ⚠️ Running Ollama on Your GPU
For optimal performance, it is highly recommended to run Ollama on your GPU if your computer supports it. On my machine, running some models on the CPU can take up to **10 minutes**, so leveraging GPU acceleration significantly improves speed.

You can find the list of available Ollama images here:  
 [Ollama Docker Hub](https://hub.docker.com/r/ollama/ollama/tags)
Depending on your **GPU type**, you may need to customize your `docker-compose` file to enable GPU support. Follow the official Docker documentation for guidance:  
[Docker Compose GPU Support](https://docs.docker.com/compose/how-tos/gpu-support/)

On my system, the model runs on a Radeon 6700XT.
If you're using a different GPU, you may need to modify the Ollama Docker Compose configuration to match your setup.
```bash
  ollama:
    image: ollama/ollama:0.5.13-rocm
    ports:
      - "11434:11434"
    devices:
      - "/dev/kfd"
      - "/dev/dri"
    group_add:
      - "video"
    depends_on:
      - php-fpm
    environment:
      - ROC_ENABLE=1
      - HSA_OVERRIDE_GFX_VERSION=10.3.0
      - LD_LIBRARY_PATH=/opt/rocm/lib
    volumes:
      - open-webui-local:/app/backend/data
    networks:
      - symfony
```

# 📚 Useful Links
- [Symfony Documentation](https://symfony.com/doc/7.0/index.html)
-  [Composer](https://getcomposer.org/doc/)

#  📝 License
This project is licensed under the MIT License.


## 💡 Feedback and Contributions

I welcome feedback, suggestions, and contributions to help improve this project! If you have an idea for a new feature, found a bug, or have any other feedback, feel free to create an issue.

### How to Get Involved:
1. **Propose Enhancements:**  
   Have an idea to improve the project? Open a new issue and describe your suggestion in detail.  
   👉 [Create an enhancement issue](https://github.com/mb2dev/Symfony-Rag/issues/new?labels=enhancement&template=feature_request.md)

2. **Report Bugs:**  
   Encountered a bug? Let us know by creating a bug report issue.  
   👉 [Create a bug report issue](https://github.com/mb2dev/Symfony-Rag/issues/new?labels=bug&template=bug_report.md)

3. **Ask Questions or Request Features:**  
   If you're unsure about something or want a specific feature, open a general issue.  
   👉 [Create a general issue](https://github.com/mb2dev/Symfony-Rag/issues/new)

### Guidelines:
- Provide as much detail as possible to help us understand your request.
- Be respectful and constructive in your communication.
