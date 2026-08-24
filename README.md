# 🎠 Vidal Slider Block

Bloco nativo do Gutenberg para criar **sliders de imagens** no WordPress, sem shortcodes e sem plugins pesados de terceiros.

![Versão](https://img.shields.io/badge/vers%C3%A3o-0.1.0-blue)
![Status](https://img.shields.io/badge/status-em%20constru%C3%A7%C3%A3o-orange)
![Licença](https://img.shields.io/badge/licen%C3%A7a-GPL--2.0--or--later-green)

> 🚧 **Em construção!** As opções de edição já funcionam no editor, mas o autoplay e a navegação por bolinhas no front-end ainda estão sendo implementados. Este README vai evoluir junto com o plugin.

---

## ✨ Funcionalidades

- 🖼️ Seleção de múltiplas imagens direto da Biblioteca de Mídia do WordPress
- 📐 Layout em largura do conteúdo (*boxed*) ou tela cheia (*full width*)
- ▶️ Autoplay configurável (ligar/desligar)
- ⏱️ Intervalo entre slides ajustável (1 a 10 segundos)
- 🔘 Navegação por bolinhas (*dots*) quando há mais de uma imagem
- ⚡ Bloco **dinâmico** — a marcação é gerada no servidor a cada carregamento da página

---

## 📦 Instalação

### Opção 1 — Upload manual

1. Baixe (ou clone) este repositório.
2. Envie a pasta `vidal-slider-block` para `/wp-content/plugins/`.
3. Ative o plugin em **Plugins** no painel do WordPress.
4. No editor de blocos, adicione o bloco **Vidal Slider Block** (categoria "Widgets").

### Opção 2 — A partir do código-fonte (desenvolvimento)

Se você clonou o repositório diretamente, é necessário compilar os assets antes de ativar o plugin:

```bash
npm install
npm run build
```

Isso gera a pasta `build/`, que é o que o plugin de fato registra e carrega no WordPress.

---

## 🚀 Como usar

1. No editor, clique em **Adicionar bloco** e procure por **"Vidal Slider Block"**.
2. Clique em **Adicionar imagens** e selecione (ou envie) uma ou mais imagens na Biblioteca de Mídia.
3. No painel lateral, em **Configurações do Slider**, ajuste:

   | Opção | O que faz |
   |---|---|
   | 🖥️ **Full width** | Faz o slider ocupar toda a largura da tela |
   | ▶️ **Autoplay** | Liga/desliga o avanço automático dos slides |
   | ⏱️ **Intervalo entre slides** | Define, em segundos, o tempo entre uma troca e outra (com autoplay ativo) |

4. Para trocar as imagens depois, use **Editar imagens**; para remover uma imagem específica, passe o mouse sobre ela e clique em **Remover**.
5. Publique ou atualize a página/post para ver o slider no front-end.

---

## ❓ Perguntas frequentes

**O slider funciona sem JavaScript no front-end?**
A marcação (slides e bolinhas de navegação) é sempre renderizada no servidor. O comportamento interativo (autoplay e clique nas bolinhas) depende do script de front-end, que ainda está em desenvolvimento nesta versão.

**Posso usar vídeos ou outros tipos de mídia?**
Não, por enquanto o bloco aceita apenas imagens.

**Preciso rodar `npm run build` sempre que atualizar o plugin?**
Só se você estiver trabalhando a partir do código-fonte (pasta `src/`). Se você baixou uma versão já empacotada/zipada do plugin, a pasta `build/` já vem pronta.

---

## 🗺️ Changelog

### 0.1.0
- 🎉 Versão inicial: seleção de imagens, configurações de layout/autoplay/intervalo e renderização dinâmica no front-end.

---

## 📄 Licença

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)

---

Feito com 💙 por [Pedro Vidal](https://github.com/pedrorvidal)
