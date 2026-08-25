# 🎠 Vidal Slider Block

Bloco nativo do Gutenberg para criar **sliders de imagens** no WordPress, sem shortcodes e sem plugins pesados de terceiros.

![Versão](https://img.shields.io/badge/vers%C3%A3o-0.1.0-blue)
![Status](https://img.shields.io/badge/status-funcional-brightgreen)
![Licença](https://img.shields.io/badge/licen%C3%A7a-GPL--2.0--or--later-green)

> ✅ Autoplay e navegação por bolinhas já funcionam no front-end. Este README vai evoluir junto com o plugin.

---

## ✨ Funcionalidades

- 🖼️ Seleção de múltiplas imagens direto da Biblioteca de Mídia do WordPress
- 📐 Layout em largura do conteúdo (*boxed*) ou tela cheia (*full width*)
- ▶️ Autoplay configurável (ligar/desligar), com pausa automática ao passar o mouse sobre o slider
- ⏱️ Intervalo entre slides ajustável (1 a 10 segundos)
- 🔘 Navegação por bolinhas (*dots*) clicáveis quando há mais de uma imagem
- ⚡ Bloco **dinâmico** — a marcação é gerada no servidor a cada carregamento da página

---

## ✅ Requisitos

| Requisito | Versão |
|---|---|
| 🐘 WordPress | 6.8 ou superior |
| 🐘 PHP | 7.4 ou superior |
| 🟢 Node.js | 18+ (recomendado, para compatibilidade com `@wordpress/scripts`) |
| 📦 npm | instalado junto com o Node.js |

---

## 📦 Instalação (uso normal do plugin)

Use este caminho se você só quer **usar** o plugin em um site WordPress, sem mexer no código.

1. Baixe uma versão já empacotada do plugin (zip) ou clone este repositório.
2. Envie a pasta `vidal-slider-block` para `/wp-content/plugins/` do seu site.
3. Ative o plugin em **Plugins** no painel do WordPress.
4. No editor de blocos, adicione o bloco **Vidal Slider Block** (categoria "Widgets").

> ⚠️ Se você clonou o repositório diretamente (em vez de baixar um zip já empacotado), a pasta `build/` não existe ainda — o plugin **não vai funcionar** até você gerá-la. Siga a seção [🛠️ Desenvolvimento](#️-desenvolvimento) abaixo.

---

## 🛠️ Desenvolvimento

Use este caminho se você vai **editar o código** do bloco (JS, SCSS, PHP).

### 1. Coloque o projeto no lugar certo

Este plugin precisa rodar dentro de uma instalação real do WordPress. Clone (ou já tenha clonado) o repositório direto dentro de `wp-content/plugins/`:

```bash
cd /caminho/do/seu/wordpress/wp-content/plugins/
git clone git@github.com:pedrorvidal/vidal-slider-block.git
cd vidal-slider-block
```

### 2. Instale as dependências

```bash
npm install
```

### 3. Compile os assets

```bash
npm run start   # modo desenvolvimento, com watch (recompila a cada alteração)
```

ou, para gerar uma build de produção (sem watch):

```bash
npm run build
```

Qualquer um dos dois comandos gera/atualiza a pasta `build/`, que é o que o `vidal-slider-block.php` de fato registra e carrega no WordPress — **o plugin lê de `build/`, nunca direto de `src/`**.

### 4. Ative o plugin

Com a `build/` gerada, vá em **Plugins** no wp-admin e ative o **Vidal Slider Block** normalmente. Enquanto `npm run start` estiver rodando, qualquer alteração em `src/` recompila automaticamente — basta recarregar a página para ver o resultado.

### 📜 Scripts disponíveis

| Comando | O que faz |
|---|---|
| `npm run start` | Build de desenvolvimento com watch (recompila ao salvar) |
| `npm run build` | Build de produção, otimizada, para `build/` |
| `npm run format` | Formata o código automaticamente (`wp-scripts format`) |
| `npm run lint:js` | Lint do JavaScript |
| `npm run lint:css` | Lint do SCSS/CSS |
| `npm run plugin-zip` | Empacota o plugin em um `.zip` pronto pra distribuir |

### 🗂️ Estrutura do projeto

```
vidal-slider-block/
├── vidal-slider-block.php     # bootstrap do plugin, registra o bloco a partir de build/
├── src/
│   └── vidal-slider-block/
│       ├── block.json          # nome, atributos e wiring dos assets do bloco
│       ├── index.js            # registra o bloco no editor
│       ├── edit.js             # UI do bloco no editor (seleção de imagens, configurações)
│       ├── save.js             # sempre retorna null — bloco é dinâmico
│       ├── render.php          # markup do front-end, gerado no servidor
│       ├── view.js             # script de front-end (autoplay, navegação)
│       ├── style.scss          # estilos usados no editor e no front-end
│       └── editor.scss         # estilos usados só no editor
└── build/                      # gerado por npm run start/build — não editar à mão
```

---

## 🚀 Como usar (wp-admin)

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
A marcação (slides e bolinhas de navegação) é sempre renderizada no servidor, mas o comportamento interativo (autoplay, troca de slide e clique nas bolinhas) depende do script de front-end (`view.js`). Sem JavaScript, apenas o primeiro slide fica visível.

**Posso usar vídeos ou outros tipos de mídia?**
Não, por enquanto o bloco aceita apenas imagens.

**Preciso rodar `npm run build` sempre que atualizar o plugin?**
Só se você estiver trabalhando a partir do código-fonte (pasta `src/`). Se você baixou uma versão já empacotada/zipada do plugin, a pasta `build/` já vem pronta.

---

## 🗺️ Changelog

### 0.1.0
- 🎉 Versão inicial: seleção de imagens, configurações de layout/autoplay/intervalo e renderização dinâmica no front-end.
- ▶️ Implementado o comportamento do slider no front-end (`view.js`): troca de slides, autoplay com pausa no hover e navegação por bolinhas.
- 🎨 Adicionados os estilos (`style.scss`) para trilha, slides e bolinhas de navegação (`.vidal-slider__*`).

---

## 📄 Licença

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)

---

Feito com 💙 por [Pedro Vidal](https://github.com/pedrorvidal)
